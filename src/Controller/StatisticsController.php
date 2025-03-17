<?php

namespace App\Controller;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
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
    public function getStatisticsData(Request $request)
    {

        $period = $request->query->get('period');
        $category = $request->query->get('category');
        $type = $request->query->get("type");
        dump($category, $type, $period);


        // $statistics = [];


        $message = "coucou, je suis un message que j'envoie depuis l'API!!";

        // switch ($category) {
        //     case 'all-items':
        //         $statistics = $statisticsService->getStatisticsByPeriod($period, $item);
        //         break;
        //     case 'vetements':
        //         $statistics = $statisticsService->getStatisticsByPeriod($period, $item, 'clothing');
        //         break;
        //     case 'ventes':
        //         $statistics = $statisticsService->getSalesStatistics($period);
        //         break;
        //     case 'visiteurs':
        //         $statistics = $statisticsService->getVisitorStatistics($period);
        //         break;
        // }



        return $this->json([
            'categorie' => $category,
            'type' => $type,
            'periode' => $period,

        ]);
    }
}





    //     #[Route('/tableau-de-bord/api/statistiques/period', name: 'api_statistics_period', methods: ['GET'])]
    //     public function getStatisticsDataForPeriod(DonationRepository $donationRepository): JsonResponse
    //     {
    //         $periodType = $_GET['period_type'] ?? null;
    //         $periodValue = $_GET['period_value'] ?? null;

    //         $donationData = [];

    //         if ($periodType === 'month' && $periodValue !== null) {
    //             $donationData = $donationRepository->findTotalDonationsByMonth($periodValue);
    //         } elseif ($periodType === 'year' && $periodValue !== null) {
    //             $donationData = $donationRepository->findDonationsByYear($periodValue);
    //         } elseif ($periodType === 'day' && $periodValue !== null) {
    //             $donationData = $donationRepository->findDonationsByDay($periodValue);
    //         } else {
    //             return $this->json(['error' => 'Invalid period type or value'], 400);
    //         }

    //         return $this->json([
    //             'donations' => $donationData,
    //         ]);
    //     }




// #[Route('/api/statistiques', name: 'api_statistics_data')]
// class StatisticsController extends AbstractController
// {
//     #[Route('/total-poids', name: 'api_statistics_total_weight', methods: ['GET'])]
//     public function getTotalWeight(Request $request)
//     {
//         // Récupérer les paramètres de la requête
//         $type = $request->query->get('type');
//         $period = $request->query->get('period');

//         // Valider les paramètres
//         if (!in_array($type, ['incoming', 'outgoing'])) {
//             return $this->json(['error' => 'Type invalide'], 400);
//         }

//         if (!in_array($period, ['daily', 'monthly', 'yearly'])) {
//             return $this->json(['error' => 'Période invalide'], 400);
//         }

//         // Récupérer les données (à adapter selon votre logique métier)
//         $data = $this->getDataForTotalWeight($type, $period);

//         // Retourner les données sous forme de réponse JSON
//         return $this->json($data);
//     }

//     private function getDataForTotalWeight(string $type, string $period): array
//     {
//         // Logique pour récupérer les données en fonction du type et de la période
//         // Remplacez ceci par une requête à votre base de données

//         $labels = [];
//         $values = [];

//         if ($period === 'daily') {
//             $labels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
//             $values = [10, 20, 15, 25, 30];
//         } elseif ($period === 'monthly') {
//             $labels = ['Janvier', 'Février', 'Mars', 'Avril'];
//             $values = [100, 150, 200, 250];
//         } elseif ($period === 'yearly') {
//             $labels = ['2020', '2021', '2022', '2023'];
//             $values = [1000, 1500, 2000, 2500];
//         }

//         return [
//             'labels' => $labels,
//             'values' => $values,
//         ];
//     }
// }
