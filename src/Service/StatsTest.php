<?php


namespace App\Service;

use App\Repository\DonationRepository;
use DateTime;

class StatsTest
{
    public function __construct(
        private DonationRepository $donationRepository,
    ) {}

    public function getDonationStatistics($period)
    {
        switch ($period) {
            case 'monthly':

                $donationsData = $this->donationRepository->findTotalWeightDonationsByMonth();
                // Initialiser un tableau de 12 mois avec des valeurs à 0
                $monthlyWeightData = array_fill(0, 12, 0);

                // Remplir le tableau avec les données disponibles
                foreach ($donationsData as $data) {
                    $monthIndex = $data['month'] - 1; // Ajuster l'index (1 = janvier, donc on fait -1 pour avoir un index 0-based)
                    $monthlyWeightData[$monthIndex] = $data['totalWeight'];
                }

                // Retourner les données avec les mois remplis
                return $monthlyWeightData;

            case 'yearly':
                return $this->donationRepository->findTotalWeightDonationsByYear();

            default:
                return ['error' => 'Invalid period'];
        }
    }
}
