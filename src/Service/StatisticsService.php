<?php


namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;

class StatisticsService
{
    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
    ) {}


    public function getStatisticsByPeriod($repository, $period, $category, $year, $month, $type): array
    {
        $data = [];

        switch ($period) {
            case 'monthly':
                $data = $this->getMonthlyData($repository, $category, $year, $type);
                break;

            case 'yearly':
                $data = $this->getYearlyData($repository, $category, $type);
                break;

            case 'daily':
                $data = $this->getDailyData($repository, $category, $year, $month, $type);
                break;

            default:
                return ['error' => "Invalid period: {$period}"];
        }

        return $data;
    }

    // public function getStatisticsByPeriod($repository, $period, $category, $year, $month, $type): array
    // {

    //     dump($type);
    //     if ($type === 'both') {
    //         // Pour 'both', on récupère séparément les 2 types
    //         switch ($period) {
    //             case 'monthly':
    //                 $entrant = $this->getMonthlyData($repository, $category, $year, 'entrant');
    //                 $sortant = $this->getMonthlyData($repository, $category, $year, 'sortant');
    //                 break;

    //             case 'yearly':
    //                 $entrant = $this->getYearlyData($repository, $category, 'entrant');
    //                 $sortant = $this->getYearlyData($repository, $category, 'sortant');
    //                 break;

    //             case 'daily':
    //                 $entrant = $this->getDailyData($repository, $category, $year, $month, 'entrant');
    //                 $sortant = $this->getDailyData($repository, $category, $year, $month, 'sortant');
    //                 break;

    //             default:
    //                 return ['error' => "Invalid period: {$period}"];
    //         }

    //         return [
    //             'entrant' => $entrant,
    //             'sortant' => $sortant,
    //         ];
    //     }

    //     // Sinon, cas simple
    //     switch ($period) {
    //         case 'monthly':
    //             $data = $this->getMonthlyData($repository, $category, $year, $type);
    //             break;

    //         case 'yearly':
    //             $data = $this->getYearlyData($repository, $category, $type);
    //             break;

    //         case 'daily':
    //             $data = $this->getDailyData($repository, $category, $year, $month, $type);
    //             break;

    //         default:
    //             return ['error' => "Invalid period: {$period}"];
    //     }

    //     return $data;
    // }





    private function getMonthlyData($repository, $category, $year, $type): array
    {

        $data = $repository->findTotalDataByMonth($repository, $category, $year, $type);

        $monthlyData = array_fill(0, 12, 0);

        foreach ($data as $entry) {

            $monthIndex = $entry['month'] - 1;
            $monthlyData[$monthIndex] = $entry['totalData'];
        }

        return $monthlyData;
    }


    private function getYearlyData($repository, $category, $type): array
    {
        return $repository->findTotalDataByYear($repository, $category, $type);
    }

    private function getDailyData($repository, $category, $year, $month, $type)
    {
        if ($month) {

            [$year, $month] = explode('-', $month);
            $year = (int) $year;
            $month = (int) $month;

            $data = $repository->findTotalDataByDayForMonth($repository, $category, $year, $month, $type);


            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dailyData = array_fill(1, $daysInMonth, 0);

            foreach ($data as $entry) {
                $dayIndex = (int) $entry['day'];
                $dailyData[$dayIndex] = $entry['totalData'];
            }

            return array_values($dailyData);
        }

        return [];
    }
}
