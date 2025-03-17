<?php



namespace App\Service;

use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use App\Repository\VisitorRepository;
use App\Repository\SaleRepository;
use DateTime;

class Statistics
{
    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
        private VisitorRepository $visitorRepository,
        private SaleRepository $salesRepository
    ) {}

    public function getDonationStatistics($period)
    {
        switch ($period) {
            // case 'daily':
            //     return $this->donationRepository->findTotalWeightDonationsByDay($date);
            case 'monthly':
                return $this->donationRepository->findTotalWeightDonationsByMonth();
            case 'yearly':
                return $this->donationRepository->findTotalWeightDonationsByYear();
            default:
                return ['error' => 'Invalid period chien'];
        }
    }
}
