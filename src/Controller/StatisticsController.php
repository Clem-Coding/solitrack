<?php

namespace App\Controller;


use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\DonationRepository;
use App\Repository\VisitorRepository;
use App\Repository\SaleRepository;
use App\Repository\SalesItemRepository;
use App\Service\MonthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ObjectRepository;

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
    public function index(string $category, MonthService $monthService): Response
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
    public function getStatisticsData(Request $request, StatisticsService $statsTest): JsonResponse
    {
        $category = $request->query->get('category');
        $type = $request->query->get("type");
        $period = $request->query->get('period');

        $year = $request->query->get("year");
        $month = $request->query->get("month");

        $repository = $this->getRepositoryForCategory($category, $type);

        $statistics = [];


        if ($type === 'both' && ($category === 'articles' || $category === 'vetements')) {
            $repoIncoming = $this->getRepositoryForCategory($category, 'incoming');
            $repoOutgoing = $this->getRepositoryForCategory($category, 'outgoing');

            $dataIncoming = $statsTest->getStatisticsByPeriod($repoIncoming, $period, $category, $year, $month, 'incoming');
            $dataOutgoing = $statsTest->getStatisticsByPeriod($repoOutgoing, $period, $category, $year, $month, 'outgoing');

            $statistics = [
                'incoming' => $dataIncoming,
                'outgoing' => $dataOutgoing,
            ];
        } else {
            $repo = $this->getRepositoryForCategory($category, $type);
            $statistics = $statsTest->getStatisticsByPeriod($repo, $period, $category, $year, $month, $type);
        }

        return $this->json([
            'data' => $statistics,
        ]);
    }


    private function getRepositoryForCategory($category, $type): ObjectRepository
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

                if ($type === 'both') {
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
