<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Service\PriceManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]


// ROUTE?
final class CartController extends AbstractController
{


    #[Route('/add', name: 'app_cart_add')]
    public function add(SessionInterface $session)
    {

        $salesCart = $session->get('sales_cart', []);

        // foreach ($salesCart as $salesItem) {

        //     $priceManagement->setDrinkPrice($salesItem);
        // }


        $session->set('sales_cart', $salesCart);
        dd($session->get('sales_cart'));
    }


    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session)
    {
        if ($session->has('sales_cart')) {
            $session->remove('sales_cart');
            $this->addFlash('success', 'Le panier a été vidé.');
        }

        return $this->redirectToRoute('app_sales');
    }
}
