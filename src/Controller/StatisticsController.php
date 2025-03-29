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
    #[Route('/tableau-de-bord/statistiques/{category}', name: 'app_dashboard_statistics')]
    public function index(string $category, DonationRepository $donationRepository): Response
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear, $currentYear - 5);
    
        $monthYearData = $donationRepository->getMonthsSinceCreation();

        $monthsInFrench = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];
    
        $months = [];
        foreach ($monthYearData as $month) {
            $parts = explode('-', $month['month']);
            if (count($parts) === 2) {
                $year = $parts[0];
                $monthNumber = $parts[1];
                $frenchMonth = $monthsInFrench[$monthNumber] ;
    
                $months[] = [
                    'value' => $month['month'],
                    'label' => $frenchMonth . ' ' . $year
                ];
            }
        }
    
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
                    $statistics = $statsTest->getSalesStatistics($period, $year, $month); 
                }
                break;

            case 'vetements':

                if ($type === 'incoming') {
                    $statistics = $statsTest->getIncomingClothingStats($period, $year, $month);
                } elseif ($type === 'outgoing') {
                    $statistics = $statsTest->getOutgoingClothingStats($period, $year, $month);
                }

                // $statistics = $statisticsService->getSalesStatistics($period);
                break;

            default:
                $statistics = ['error' => 'Invalid category'];
                break;
        }

        return $this->json([
            'data' => $statistics,
        ]);
    }
}
