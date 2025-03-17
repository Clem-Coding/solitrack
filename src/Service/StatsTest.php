<?php


namespace App\Service;

use App\Repository\DonationRepository;
use DateTime;

class StatsTest
{
    public function __construct(
        private DonationRepository $donationRepository,
    ) {}

    public function getDonationStatistics($period, $year = null, $date = null)
    {


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
                if ($date) {
                    dump("la date dans le service", $date);
                    $donationsData = $this->donationRepository->findTotalWeightDonationsByDay($date);
                } else {
                    $donationsData = $this->donationRepository->findTotalWeightDonationsByDay(new DateTime());
                }

                return $donationsData;;
            default:
                return ['error' => "Invalid period: chat"]; // Fournir plus de contexte dans le message d'erreur
        }
    }
}
