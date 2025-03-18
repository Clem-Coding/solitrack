<?php

namespace App\Controller;


use App\Service\StatsTest;
// use App\Repository\DonationRepository;
// use App\Repository\SalesItemRepository;
// use App\Service\StatisticsService;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\DonationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    #[Route('/tableau-de-bord/statistiques/{category}', name: 'app_dashboard_statistics', defaults: ['category' => 'all-items'])]
    public function index(string $category, DonationRepository $donationRepository): Response
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear, $currentYear - 5);


        $rawMonths = $donationRepository->findAvailableMonths();

        $monthsInFrench = [
            'January' => 'janvier',
            'February' => 'février',
            'March' => 'mars',
            'April' => 'avril',
            'May' => 'mai',
            'June' => 'juin',
            'July' => 'juillet',
            'August' => 'août',
            'September' => 'septembre',
            'October' => 'octobre',
            'November' => 'novembre',
            'December' => 'décembre'
        ];

        $months = array_map(function ($item) use ($monthsInFrench) {
            $date = \DateTime::createFromFormat('Y-m', $item['month']);
            $monthName = $date->format('F');
            $monthNameInFrench = $monthsInFrench[$monthName];

            return [
                'value' => $item['month'],
                'label' => $monthNameInFrench . ' ' . $date->format('Y')
            ];
        }, $rawMonths);

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

        $period = $request->query->get('period');
        $category = $request->query->get('category');
        $type = $request->query->get("type");
        $year = $request->query->get("year");
        $month = $request->query->get("month");



        $statistics = [];




        switch ($category) {
            case 'articles':

                if ($type === 'incoming') {
                    $statistics = $statsTest->getDonationStatistics($period, $year, $month);
                } elseif ($type === 'outgoing') {
                    dump("oui oui outgoing");
                    $statistics = $statsTest->getSalesStatistics($period, $year, $month);
                }
                break;

            case 'ventes':

                // $statistics = $statisticsService->getSalesStatistics($period);
                break;

            default:
                $statistics = ['error' => 'Invalid category'];
                break;
        }



        dump("les statistiques", $statistics);


        return $this->json([
            'data' => $statistics,
        ]);
    }
}
