<?php

namespace App\Service;

use App\Entity\SalesItem;
use Symfony\Component\HttpFoundation\RequestStack;


class PriceManagementService
{
    const CATEGORY_DRINK = 4;
    const BULK_ARTICLE_CATEGORIES = [1, 2];

    // The price per drink unit is set at 1€. Update this value if the pricing rules change
    const DRINK_UNIT_PRICE = 1;

    // The current price is 1€/kg. Update these values if the pricing rules change.
    const PRICE_PER_KG = 1;




    public function __construct(protected RequestStack $requestStack,) {}


    /**
     * Set the price for drink items based on their category and quantity.
     *
     * @param SalesItem $salesItem The sales item to set the price for.
     */

    public function setDrinkPrice(SalesItem $salesItem): void
    {

        $category = $salesItem->getCategory()->getId();
        $quantity = $salesItem->getQuantity();

        if ($category === self::CATEGORY_DRINK) {

            $price = self::DRINK_UNIT_PRICE * $quantity;
            $salesItem->setPrice($price);
        }
    }


    private function roundDownToTenth(float $price): float
    {
        $roundedPrice = floor($price * 10) / 10;
        return $roundedPrice;
    }


    /**
     * Set the price based on weight for bulk articles.
     *
     * @param SalesItem $salesItem The sales item to set the price for.
     */
    public function setWeightBasedPrice(SalesItem $salesItem): void
    {
        $category = $salesItem->getCategory()->getId();
        $weight = $salesItem->getWeight();

        if ($category && in_array($category, self::BULK_ARTICLE_CATEGORIES)) {
            $totalPrice = $weight * self::PRICE_PER_KG;
            $salesItem->setPrice($this->roundDownToTenth($totalPrice));
        }
    }


    /**
     * Get the total price of the shopping cart.
     *
     * @return float The total price of the items in the cart.
     */
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



        foreach ($shoppingCart as $itemData) {
            if (in_array($itemData['id'], self::BULK_ARTICLE_CATEGORIES)) {
                $totalWeight += $itemData['weight'] ?? 0;
            }
        }


        if ($totalWeight < 1) {
            foreach ($shoppingCart as &$itemData) {
                if (in_array($itemData['id'], self::BULK_ARTICLE_CATEGORIES)) {
                    $itemData['price'] = 0;
                }
            }
        }

        //Free the reference to avoid display bugs
        unset($itemData);

        $session->set('shopping_cart', $shoppingCart);
    }
}
