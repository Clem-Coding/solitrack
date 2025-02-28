<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class SalesController extends AbstractController
{
    #[Route('/ventes', name: 'app_sales')]
    public function index(): Response
    {
        return $this->render('sales/index.html.twig', [
            'controller_name' => 'SalesController',
        ]);
    }
}
