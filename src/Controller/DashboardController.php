<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/tableau-de-bord')]
final class DashboardController extends AbstractController
{

    #[Route('', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    // #[Route('/statistiques', name: 'app_dashboard_statistics')]
    // public function statistics(): Response
    // {
    //     return $this->render('dashboard/statistics.html.twig');
    // }


    #[Route('/controle-de-caisse', name: 'app_dashboard_cash_reconciliation')]
    public function cashReconciliation(): Response
    {
        return $this->render('dashboard/cash_reconciliation.html.twig');
    }

    #[Route('/gestion-utilisateurs', name: 'app_dashboard_user_management')]
    public function userManagement(): Response
    {
        return $this->render('dashboard/user_management.html.twig');
    }


    #[Route('/parametres', name: 'app_dashboard_settings')]
    public function settings(): Response
    {
        return $this->render('dashboard/settings.html.twig');
    }
}
