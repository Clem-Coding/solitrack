<?php

namespace App\Service;

use App\Entity\SalesItem;
use Symfony\Component\HttpFoundation\RequestStack;


class PriceManagement
{

    public function __construct(protected RequestStack $requestStack,) {}

    public function setDrinkPrice(SalesItem $salesItem): void
    {

        if ($salesItem->getCategory() && $salesItem->getCategory()->getId() == 4) {

            // The price per drink unit is set at 1€. Update this value if the pricing rules change.
            $drinkUnitPrice = 1;

            $salesItem->setPrice($drinkUnitPrice);
        }
    }


    private function roundDownToTenth(float $price): float
    {
        $roundedPrice = floor($price * 10) / 10;
        return $roundedPrice;
    }


    public function setWeightBasedPrice(SalesItem $salesItem): void
    {

        // The current price is 1€/kg. Update these values if the pricing rules change.
        $pricePerKg = 1;

        $weight = $salesItem->getWeight();

        $totalPrice = $weight * $pricePerKg;

        $salesItem->setPrice($this->roundDownToTenth($totalPrice));
    }
}
