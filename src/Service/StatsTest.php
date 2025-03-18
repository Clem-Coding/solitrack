<?php


namespace App\Service;

use App\Repository\DonationRepository;
use DateTime;

class StatsTest
{
    public function __construct(
        private DonationRepository $donationRepository,
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
                        $dayIndex = (int) $data['day']; // Jour du mois (ex: 1, 2, 3...)
                        $dailyWeightData[$dayIndex] = $data['totalWeight'];
                    }

                    return array_values($dailyWeightData); // S'assurer que l'index commence bien à 0
                }

                return [];
            default:
                return ['error' => "Invalid period: chat"];
        }
    }
}
