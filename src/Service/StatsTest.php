<?php


namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;

class StatsTest
{
    // public function __construct(
    //     private DonationRepository $donationRepository,
    //     private SalesItemRepository $salesItemRepository,
    // ) {}


    public function getStatisticsByPeriod($repository, $period, $category, $year, $month) : array
    {
        $data = [];

        switch ($period) {
            case 'monthly':
                $data = $this->getMonthlyData($repository, $category, $year);
                break;
    
            case 'yearly':
                $data = $this->getYearlyData($repository, $category);
                break;
    
            case 'daily':
                $data = $this->getDailyData($repository, $category, $year, $month);
                break;
    
            default:
                return ['error' => "Invalid period: {$period}"];
        }
    
        return $data;
    }
    

    private function getMonthlyData($repository, $category, $year) : array
    {

        $data = $repository->findTotalDataByMonth($repository, $category, $year);
    
        $monthlyData = array_fill(0, 12, 0); 
        
        foreach ($data as $entry) {
    
            $monthIndex = $entry['month'] - 1;
            $monthlyData[$monthIndex] = $entry['totalData'];  
        }

        return $monthlyData;
    }
    

    private function getYearlyData($repository, $category) : array
    {
        return $repository->findTotalDataByYear($repository,$category);
    }
    
    private function getDailyData($repository, $category, $year, $month)
    {
        if ($month) {
    
            [$year, $month] = explode('-', $month);
            $year = (int) $year;
            $month = (int) $month;
    
            $data = $repository->findTotalDataByDayForMonth($repository, $category, $year, $month);
    
        
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

