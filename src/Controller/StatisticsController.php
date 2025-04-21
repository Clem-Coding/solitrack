<?php

namespace App\Controller;


use App\Service\StatsTest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\DonationRepository;
use App\Repository\VisitorRepository;
use App\Repository\SaleRepository;
use App\Repository\SalesItemRepository;
use App\Service\MonthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

class StatisticsController extends AbstractController
{

    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
        private VisitorRepository $visitorRepository,
        private SaleRepository $saleRepository
    ) {}


    #[Route('/tableau-de-bord/statistiques/{category}', name: 'app_dashboard_statistics')]
    public function index(string $category, DonationRepository $donationRepository, MonthService $monthService): Response
    {

        $currentYear = (int) date('Y');
        $years = range($currentYear, $currentYear - 5);

        $monthYearData = $this->donationRepository->getMonthsSinceCreation();

        $months = $monthService->getMonthsInFrench($monthYearData);

        return $this->render('dashboard/statistics.html.twig', [
            'category' => $category,
            'years' => $years,
            'current_year' => $currentYear,
            'months' => $months,
        ]);
    }


    #[Route('/api/statistiques/', name: 'api_statistics_data', methods: ['GET'])]
    public function getStatisticsData(Request $request, StatsTest $statsTest)
    {
        $category = $request->query->get('category');
        $type = $request->query->get("type");
        $period = $request->query->get('period');

        $year = $request->query->get("year");
        $month = $request->query->get("month");

        $repository = $this->getRepositoryForCategory($category, $type);

        $statistics = [];


        $statistics = $statsTest->getStatisticsByPeriod(
            $repository,
            $period,
            $category,
            $year,
            $month,
            $type
        );


        return $this->json([
            'data' => $statistics,
        ]);
    }


    private function getRepositoryForCategory($category, $type)
    {
        switch ($category) {
            case 'articles':
            case 'vetements':
                if ($type === 'incoming') {
                    return $this->donationRepository;
                }
                if ($type === 'outgoing') {
                    return $this->salesItemRepository;
                }
                break;

            case 'ventes':
                return $this->saleRepository;

            case 'visiteurs':
                return $this->visitorRepository;

            default:
                throw new \InvalidArgumentException("Catégorie inconnue : " . $category);
        }

        throw new \InvalidArgumentException("Type inconnu : " . $type);
    }
}
