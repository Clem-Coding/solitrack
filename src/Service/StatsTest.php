<?php


namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;

class StatsTest
{
    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
    ) {}


    private function getStatisticsByPeriod($repository, $period, $year = null, $month = null)
    {
        $data = [];
        switch ($period) {
            case 'monthly':
                $data = $repository->findTotalWeightByMonth($year);
                // dump('les data pour monthly', $data);

                $monthlyData = array_fill(0, 12, 0);
                foreach ($data as $entry) {
                    $monthIndex = $entry['month'] - 1;
                    $monthlyData[$monthIndex] = $entry['totalWeight'];
                }
                return $monthlyData;

            case 'yearly':
                return $repository->findTotalWeightByYear();

            case 'daily':
                if ($month) {
                    // dump("le moids,", $month);
                    [$year, $month] = explode('-', $month);
                    $year = (int) $year;
                    $month = (int) $month;
                    $data = $repository->findTotalWeightByDayForMonth($year, $month);
                    // dump('les data pour daily', $data);

                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $dailyData = array_fill(1, $daysInMonth, 0);

                    foreach ($data as $entry) {
                        $dayIndex = (int) $entry['day'];
                        $dailyData[$dayIndex] = $entry['totalWeight'];
                    }

                    return array_values($dailyData);
                }
                return [];
            default:
                return ['error' => "Invalid period: {$period}"];
        }
    }

    public function getDonationStatistics($period, $year = null, $month = null)
    {
        return $this->getStatisticsByPeriod($this->donationRepository, $period, $year, $month);
    }

    public function getSalesStatistics($period, $year = null, $month = null)
    {
        return $this->getStatisticsByPeriod($this->salesItemRepository, $period, $year, $month);
    }


    public function getIncomingClothingStats($period, $year = null, $month = null)
    {
        return $this->donationRepository->findTotalWeightOfClothingByPeriod($period, $year, $month);
    }

    public function getOutgoingClothingStats($period, $year = null, $month = null)
    {
        return $this->salesItemRepository->findTotalWeightOfClothingByPeriod($period, $year, $month);
    }

    // // Exemple pour ajouter un calcul pour les visiteurs (similaire aux autres)
    // public function getVisitorStatistics($period, $year = null, $month = null)
    // {
    //     return $this->getStatisticsByPeriod($this->visitorRepository, $period, $year, $month);
    // }
}
