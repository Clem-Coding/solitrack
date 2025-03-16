<?php

namespace App\Service;


use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use App\Repository\VisitorRepository;
use App\Repository\SaleRepository;
use DateTime;

class StatisticsService
{


    public function __construct(
        private DonationRepository $donationRepository,
        private SalesItemRepository $salesItemRepository,
        private VisitorRepository $visitorRepository,
        private SaleRepository $salesRepository
    ) {}

    /**
     * Méthode pour récupérer les statistiques filtrées par période et type d'article
     *
     * @param string $period La période ('daily', 'monthly', 'yearly')
     * @param string|null $item Le type d'item ('item1', 'item2', etc.)
     * @param string|null $category La catégorie (par exemple, 'clothing', 'books')
     * @return array Les statistiques filtrées
     */
    public function getStatisticsByPeriod(string $period, ?string $item = null, ?string $category = null)
    {
        // Validation du filtre période
        if (!in_array($period, ['daily', 'monthly', 'yearly'])) {
            throw new \InvalidArgumentException("Invalid period: " . $period);
        }

        $method = $this->getPeriodMethod($period);
        $date = new \DateTime();

        // Filtrer par item et catégorie si nécessaire
        switch ($item) {
            case 'item1': // Articles entrants
                return $this->donationRepository->$method($date, $category);
            case 'item2': // Articles sortants
                return $this->salesItemRepository->$method($date, $category);
            case 'item3': // Articles entrants et sortants
                return [
                    'entrants' => $this->donationRepository->$method($date, $category),
                    'sortants' => $this->salesItemRepository->$method($date, $category),
                ];
            default:
                throw new \InvalidArgumentException("Invalid item type: " . $item);
        }
    }

    /**
     * Récupère la méthode de filtrage en fonction de la période
     */
    private function getPeriodMethod(string $period): string
    {
        switch ($period) {
            case 'daily':
                return 'findByDate';
            case 'monthly':
                return 'findByMonth';
            case 'yearly':
                return 'findByYear';
            default:
                throw new \InvalidArgumentException("Invalid period: " . $period);
        }
    }



    private function getVisitorStatistics(string $period, ?string $category = null)
    {
        $method = $this->getPeriodMethod($period);
        $date = new DateTime();

        return $this->visitorRepository->$method($date, $category);
    }
}
