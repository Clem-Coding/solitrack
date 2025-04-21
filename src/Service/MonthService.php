<?php

namespace App\Service;

class MonthService
{

    private $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre'
    ];


    private $vowelMonths = ['Avril', 'Août', 'Octobre'];


    public function getMonthName(int $monthNumber): string
    {
        return $this->months[$monthNumber] ?? '';
    }


    public function getMonths(): array
    {
        return $this->months;
    }


    public function getMonthStatsTitle(int $monthNumber): string
    {
        $monthName = $this->getMonthName($monthNumber);

        if (in_array($monthName, $this->vowelMonths)) {
            return "Les statistiques d'$monthName";
        }

        return "Les statistiques du mois de $monthName";
    }

    public function getMonthsInFrench(array $monthYearData): array
    {
        $monthsInFrench = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        $months = [];

        // Parcours de chaque élément de $monthYearData pour transformer le mois et l'année
        foreach ($monthYearData as $month) {
            $parts = explode('-', $month['month']);
            if (count($parts) === 2) {
                $year = $parts[0];
                $monthNumber = $parts[1];
                $frenchMonth = $monthsInFrench[$monthNumber];

                $months[] = [
                    'value' => $month['month'],
                    'label' => $frenchMonth . ' ' . $year
                ];
            }
        }

        return $months;
    }
}
