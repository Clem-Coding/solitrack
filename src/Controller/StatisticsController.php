<?php

namespace App\Controller;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    #[Route('/tableau-de-bord/statistiques', name: 'app_dashboard_statistics')]
    public function index(): Response
    {

        return $this->render('dashboard/statistics.html.twig');
    }

    // #[Route('/tableau-de-bord/api/statistiques/', name: 'api_statistics_data', methods: ['GET'])]
    // public function getStatisticsData(DonationRepository $donationRepository, SalesItemRepository $salesItemRepository): JsonResponse
    // {

    //     $monthlyDonationWeights = $donationRepository->findMonthlyDonations();
    //     $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];


    //     $donationData = array_fill(0, 12, 0);



    //     foreach ($monthlyDonationWeights as $monthlyDonationWeight) {
    //         $month = $monthlyDonationWeight['month'] - 1;
    //         $donationData[$month] = $monthlyDonationWeights['totalWeight'];
    //     }



    //     return $this->json([
    //         'months' => $months,
    //         'donations' => $donationData,
    //         'salesWeights' => $salesWeightData,
    //     ]);
    // }





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
}



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
