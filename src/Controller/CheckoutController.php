<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutController extends AbstractController
{
    #[Route('/ventes/caisse', name: 'app_checkout')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('shopping_cart', []);


        return $this->render('sales/checkout.html.twig', [
            'cart' => $cart,
        ]);
    }
}
