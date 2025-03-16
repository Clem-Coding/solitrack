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




    public function getCartTotal(): float
    {
        $session = $this->requestStack->getSession();
        $shoppingCart = $session->get('shopping_cart', []);
        return array_sum(array_column($shoppingCart, 'price'));
    }



    /**
     * Apply the bulk pricing rule:
     * - If the weight of category 1 (bulk clothing) and 2(bulk others items) is less than 1kg → 0€ default and open pricing for the customer
     */
    public function applyBulkPricingRule(): void
    {
        $session = $this->requestStack->getSession();
        $shoppingCart = $session->get('shopping_cart', []);
        $totalWeight = 0;

        $bulkCategories = ['Vêtements vrac', 'Autres articles vrac'];


        foreach ($shoppingCart as $itemData) {
            if (in_array($itemData['category'], $bulkCategories)) {
                $totalWeight += $itemData['weight'] ?? 0;
            }
        }



        if ($totalWeight < 1) {
            foreach ($shoppingCart as &$itemData) {
                if (in_array($itemData['category'], $bulkCategories)) {
                    $itemData['price'] = 0;
                }
            }
        }

        // IMPORTANT : Libérer la référence pour éviter les bugs d'affichage
        unset($itemData);

        // dump($shoppingCart);

        // Mise à jour du panier en session
        $session->set('shopping_cart', $shoppingCart);
    }
}
