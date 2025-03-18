<?php


namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use DateTime;

class StatsTest
{
    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
    ) {}

    public function getDonationStatistics($period, $year = null, $month = null)
    {

        $donationsData = [];


        switch ($period) {
            case 'monthly':
                $donationsData = $this->donationRepository->findTotalWeightDonationsByMonth($year);
                $monthlyWeightData = array_fill(0, 12, 0);

                foreach ($donationsData as $data) {
                    $monthIndex = $data['month'] - 1;
                    $monthlyWeightData[$monthIndex] = $data['totalWeight'];
                }

                return $monthlyWeightData;

            case 'yearly':
                return $this->donationRepository->findTotalWeightDonationsByYear();

            case 'daily':
                if ($month) {

                    [$year, $month] = explode('-', $month);
                    $year = (int) $year;
                    $month = (int) $month;


                    $donationsData = $this->donationRepository->getTotalWeightByDayForMonth($year, $month);


                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                    //On initialise le tableau avec la bonne taille de jours dans le mois
                    $dailyWeightData = array_fill(1, $daysInMonth, 0);

                    // on remplit le tableau avec les données
                    foreach ($donationsData as $data) {
                        $dayIndex = (int) $data['day'];
                        $dailyWeightData[$dayIndex] = $data['totalWeight'];
                    }

                    return array_values($dailyWeightData);
                }

                return [];
            default:
                return ['error' => "Invalid period: chat"];
        }
    }


    public function getSalesStatistics($period, $year = null, $month = null)
    {
        $salesData = [];

        switch ($period) {
            case 'monthly':

                $salesData = $this->salesItemRepository->findTotalSalesByMonth($year);
                $monthlySalesData = array_fill(0, 12, 0);

                // Remplir le tableau avec les données
                foreach ($salesData as $data) {
                    $monthIndex = $data['month'] - 1;  // Le mois commence à 1, donc on le décale de 1
                    $monthlySalesData[$monthIndex] = $data['totalWeight'];
                }

                return $monthlySalesData;

            case 'yearly':

                return $this->salesItemRepository->findTotalSalesByYear();

            case 'daily':
                if ($month) {
                    // Extraire l'année et le mois
                    [$year, $month] = explode('-', $month);
                    $year = (int) $year;
                    $month = (int) $month;


                    $salesData = $this->salesItemRepository->getTotalSalesByDayForMonth($year, $month);


                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);


                    $dailySalesData = array_fill(1, $daysInMonth, 0);


                    foreach ($salesData as $data) {
                        $dayIndex = (int) $data['day'];  // Le jour du mois
                        $dailySalesData[$dayIndex] = $data['totalSales'];
                    }

                    return array_values($dailySalesData);
                }

                return [];
            default:
                return ['error' => "Invalid period: {$period}"];
        }
    }
}
