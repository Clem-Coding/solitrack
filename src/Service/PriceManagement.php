<?php

namespace App\Service;

use App\Entity\SalesItem;
use Symfony\Component\HttpFoundation\RequestStack;


class PriceManagement
{

    public function __construct(protected RequestStack $requestStack,) {}

    public function setDrinkPrice(SalesItem $salesItem): void
    {

        $category = $salesItem->getCategory()->getId();
        $quantity = $salesItem->getQuantity();



        if ($category && $category === 4) {

            // The price per drink unit is set at 1€. Update this value if the pricing rules change.
            $drinkUnitPrice = 1;

            $price = $drinkUnitPrice * $quantity;


            $salesItem->setPrice($price);
        }
    }


    private function roundDownToTenth(float $price): float
    {
        $roundedPrice = floor($price * 10) / 10;
        return $roundedPrice;
    }


    public function setWeightBasedPrice(SalesItem $salesItem): void
    {
        $category = $salesItem->getCategory()->getId();


        // The current price is 1€/kg. Update these values if the pricing rules change.
        $pricePerKg = 1;

        $totalPrice = 789;

        $weight = $salesItem->getWeight();

        if ($category && ($category === 1 || $category === 2)) {

            $totalPrice = $weight * $pricePerKg;

            $salesItem->setPrice($this->roundDownToTenth($totalPrice));
        }
    }
}
