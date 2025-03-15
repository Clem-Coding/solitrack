<?php

namespace App\Controller;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\UX\Chartjs\Model\Chart;

class StatisticsController extends AbstractController
{
    #[Route('/tableau-de-bord/statistiques', name: 'app_dashboard_statistics')]
    public function index(DonationRepository $donationRepository, SalesItemRepository $salesItemRepository): Response
    {

        $monthlyDonations = $donationRepository->findMonthlyDonations();
        $monthlySalesWeights = $salesItemRepository->findMonthlySalesItems();




        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];


        $donationData = array_fill(0, 12, 0);  // Remplir un tableau avec 0 pour chaque mois
        $monthlySalesWeightData = array_fill(0, 12, 0);


        // Remplir donationData avec les poids totaux par mois
        foreach ($monthlyDonations as $donation) {
            $month = $donation['month'] - 1; // PHP compte les mois de 0 à 11, donc on soustrait 1
            $donationData[$month] = $donation['totalWeight'];
        }


        foreach ($monthlySalesWeights as $monthlySalesWeight) {
            $month = $monthlySalesWeight['month'] - 1;
            $monthlySalesWeightData[$month] = $monthlySalesWeight['totalWeight'];
        }




        // dd($chart);

        // Retourner la vue avec le graphique
        return $this->render('dashboard/statistics.html.twig', []);
    }
}
