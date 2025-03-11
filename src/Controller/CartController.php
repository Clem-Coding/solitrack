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

        $salesCart = $session->get('shopping_cart', []);

        // foreach ($salesCart as $salesItem) {

        //     $priceManagement->setDrinkPrice($salesItem);
        // }


        $session->set('shopping_cart', $salesCart);
    }


    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session)
    {
        if ($session->has('shopping_cart')) {
            $session->remove('shopping_cart');
            $this->addFlash('success', 'Le panier a été vidé.');
        }

        return $this->redirectToRoute('app_sales');
    }
}
