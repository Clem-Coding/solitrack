<?php

namespace App\Controller\Dashboard;

use App\Repository\CashRegisterSessionRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED')]
class CashRegisterController extends AbstractController
{
    #[Route('/gestion-de-caisse', name: 'app_dashboard_cash_register')]
    public function index(CashRegisterSessionRepository $cashRegisterSessionRepository): Response
    {

        $session = $cashRegisterSessionRepository->findAnyOpenSession();

        // if (!$session) {
        //     $this->addFlash('warning', 'Aucune session de caisse ouverte.');
        //     return $this->redirectToRoute('app_sales');
        // }

        $sales = $session->getSales()->toArray();

        $totalPrice = 0;
        $totalCard = 0;
        $totalCash = 0;

        foreach ($sales as $sale) {
            $totalPrice += (float) $sale->getTotalPrice();
            $totalPrice += (float) $sale->getPwywAmount();
            $totalPrice += (float) $sale->getKeepChange();
            $totalCard += (float) $sale->getCardAmount();
            $totalCash += (float) $sale->getCashAmount();
        }


        $openedBy = $session->getOpenedBy()?->getFirstName();
        $openingAt = $session->getOpeningAt()?->format('d/m/Y à H\hi');
        $cashFloat = $session->getCashFloat();


        return $this->render('dashboard/cash_register.html.twig', [
            'openedBy' => $openedBy,
            'openingAt' => $openingAt,
            'cashFloat' => $cashFloat,
            'totalPrice' => $totalPrice,
            'totalCard' => $totalCard,
            'totalCash' => $totalCash,
        ]);
    }
}
