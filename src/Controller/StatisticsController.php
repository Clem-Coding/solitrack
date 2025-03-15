<?php

namespace App\Controller;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    #[Route('/tableau-de-bord/statistiques', name: 'app_dashboard_statistics')]
    public function index(): Response
    {
        // Ici, tu rendras la page HTML contenant le graphique
        return $this->render('dashboard/statistics.html.twig');
    }

    #[Route('/tableau-de-bord/api/statistiques/', name: 'api_statistics_data', methods: ['GET'])]
    public function getStatisticsData(DonationRepository $donationRepository, SalesItemRepository $salesItemRepository): JsonResponse
    {

        $monthlyDonations = $donationRepository->findMonthlyDonations();
        // $monthlySalesWeights = $salesItemRepository->findMonthlySalesItems();
        // dd($monthlySalesWeights);

        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];


        $donationData = array_fill(0, 12, 0);

        $salesWeightData = array_fill(0, 12, 0);


        foreach ($monthlyDonations as $donation) {
            $month = $donation['month'] - 1;
            $donationData[$month] = $donation['totalWeight'];
        }

        // dd($donationData);


        // foreach ($monthlySalesWeights as $salesWeight) {
        //     $month = $salesWeight['month'] - 1;
        //     $salesWeightData[$month] = $salesWeight['totalWeight'];
        // }


        return $this->json([
            'months' => $months,
            'donations' => $donationData,
            'salesWeights' => $salesWeightData,
        ]);
    }
}
