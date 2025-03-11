<?php

namespace App\Service;

use App\Entity\SalesItem;


class PriceManagement
{

    public function setDrinkPrice(SalesItem $salesItem): void
    {

        if ($salesItem->getCategory() && $salesItem->getCategory()->getId() == 4) {

            $salesItem->setPrice(1);
        }
    }
}
