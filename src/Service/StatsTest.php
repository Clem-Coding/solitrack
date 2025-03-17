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
                    dump("la month dans le service", $month);
                    $donationsData = $this->donationRepository->getTotalWeightByDayForMonth($month);
                }

                return $donationsData;;
            default:
                return ['error' => "Invalid period: chat"];
        }
    }
}
