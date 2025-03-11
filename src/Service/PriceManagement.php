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
        $minimumWeightThreshold = 1;


        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $session = $request->getSession();
            $salesCart = $session->get('sales_cart', []);
        }


        $totalWeightForCategory1And2 = 0;

        if (!empty($salesCart)) {


            foreach ($salesCart as $item) {
                if (isset($item['category']) && isset($item['weight'])) {
                    if ($item['category'] === 'Vêtements vrac' || $item['category'] === 'Autres articles vrac') {
                        $weight = (float) $item['weight'];
                        $totalWeightForCategory1And2 += $weight;
                    }
                }
            }
        } else {
            $totalWeightForCategory1And2 = $salesItem->getWeight();
        }

        $totalPrice = 0;

        if ($salesItem->getCategory() && $salesItem->getCategory()->getId() == 1 || $salesItem->getCategory()->getId() == 2) {

            $WeightForEachCategory = $salesItem->getWeight();

            if ($totalWeightForCategory1And2 >= $minimumWeightThreshold) {
                $totalPrice = $WeightForEachCategory * $pricePerKg;
            }

            $salesItem->setPrice($this->roundDownToTenth($totalPrice));
        }
    }
}
