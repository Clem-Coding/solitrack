<?php


namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\OutgoingWeighingRepository;
use App\Repository\SalesItemRepository;

class StatisticsService
{
    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
        private OutgoingWeighingRepository $outgoingWeighingRepository
    ) {}

    public function getStatisticsByPeriod($repository, $period, $category, $year, $month, $type): array
    {

        // special case : we neeed to combine two data sources (sales items + outgoing weighings)
        if ($category === 'articles' && $type === 'outgoing') {
            $dataOutgoing = $this->getDataByPeriod($repository, $period, $category, $year, $month, $type); // from sales items
            $dataWeighing = $this->getDataByPeriod($this->outgoingWeighingRepository, $period, $category, $year, $month, $type); //from outgoing weighings

            // if period is yearly, merge specially
            if ($period === 'yearly') {
                return $this->mergeYearlyStats($dataOutgoing, $dataWeighing);
            }

            // else use array_map to sum corresponding entries
            return array_map(fn($a, $b) => $a + $b, $dataOutgoing, $dataWeighing);
        }

        return $this->getDataByPeriod($repository, $period, $category, $year, $month, $type);
    }

    private function getDataByPeriod($repository, $period, $category, $year, $month, $type): array
    {
        return match ($period) {
            'monthly' => $this->getMonthlyData($repository, $category, $year, $type),
            'yearly' => $this->getYearlyData($repository, $category, $type),
            'daily' => $this->getDailyData($repository, $category, $year, $month, $type),
            default => ['error' => "Invalid period: {$period}"]
        };
    }

    /**
     * Merges two yearly statistics arrays by summing totalData for matching years.
     */
    private function mergeYearlyStats(array $a, array $b): array
    {
        // Convert each array into an associative array keyed by year
        $yearsA = [];
        foreach ($a as $entry) {
            $yearsA[$entry['year']] = $entry['totalData'];
        }
        $yearsB = [];
        foreach ($b as $entry) {
            $yearsB[$entry['year']] = $entry['totalData'];
        }
        // Get the list of all years present in at least one of the two arrays
        $allYears = array_unique(array_merge(array_keys($yearsA), array_keys($yearsB)));
        sort($allYears);

        $result = [];
        // For each year, add the totalData values from both sources (use 0 if absent)
        foreach ($allYears as $year) {
            $result[] = [
                'year' => $year,
                'totalData' => ($yearsA[$year] ?? 0) + ($yearsB[$year] ?? 0)
            ];
        }
        return $result;
    }


    private function getMonthlyData($repository, $category, $year, $type): array
    {
        $data = $repository->findTotalDataByMonth($year, $type, $category);

        $monthlyData = array_fill(0, 12, 0);

        foreach ($data as $entry) {

            $monthIndex = $entry['month'] - 1;
            $monthlyData[$monthIndex] = $entry['totalData'];
        }

        return $monthlyData;
    }


    private function getYearlyData($repository, $category, $type): array
    {
        return $repository->findTotalDataByYear($type, $category);
    }

    private function getDailyData($repository, $category, $year, $month, $type)
    {
        if ($month) {

            [$year, $month] = explode('-', $month);
            $year = (int) $year;
            $month = (int) $month;

            $data = $repository->findTotalDataByDayForMonth(
                $year,
                $month,
                $type,
                $category
            );

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
