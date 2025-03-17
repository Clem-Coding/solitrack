<?php

namespace App\Controller;


use App\Service\StatsTest;
// use App\Repository\DonationRepository;
// use App\Repository\SalesItemRepository;
// use App\Service\StatisticsService;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    #[Route('/tableau-de-bord/statistiques/{category}', name: 'app_dashboard_statistics', defaults: ['category' => 'all-items'])]
    public function index(string $category): Response
    {

        $currentYear = (int) date('Y');;
        $years = range($currentYear, $currentYear - 5);
        // dump($category);



        return $this->render('dashboard/statistics.html.twig', [
            'category' => $category,
            'years' => $years,
            'current_year' => $currentYear,
        ]);
    }

    #[Route('/api/statistiques/', name: 'api_statistics_data', methods: ['GET'])]
    public function getStatisticsData(Request $request, StatsTest $statsTest)
    {

        $period = $request->query->get('period');
        $category = $request->query->get('category');
        $type = $request->query->get("type");
        $year = $request->query->get("year");
        $date = $request->query->get("date");
        dump("la date dans le controller", $date);


        $statistics = [];




        switch ($category) {
            case 'articles':
                // Appel du service pour la catégorie "articles"
                if ($type === 'incoming') {
                    $statistics = $statsTest->getDonationStatistics($period, $year, $date);
                } elseif ($type === 'outgoing') {
                    // $statistics = $statisticsService->getSalesStatistics($period);
                }
                break;

            case 'ventes':
                // Appel du service pour la catégorie "ventes"
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
