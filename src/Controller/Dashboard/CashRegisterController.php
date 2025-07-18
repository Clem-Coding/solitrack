<?php

namespace App\Controller\Dashboard;

use App\Entity\CashRegisterClosure;
use App\Entity\CashRegisterSession;
use App\Enum\CashMovementAction;
use App\Entity\User;
use App\Entity\CashMovement;
use App\Form\CashMovementType;
use App\Form\CashRegisterClosureType;
use App\Form\CoinCountType;
use App\Repository\CashRegisterClosureRepository;
use App\Repository\CashRegisterSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('IS_AUTHENTICATED')]
class CashRegisterController extends AbstractController
{
    #[Route('/gestion-de-caisse', name: 'app_dashboard_cash_register')]
    public function index(
        #[CurrentUser] User $user,
        CashRegisterSessionRepository $cashRegisterSessionRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        CashRegisterClosureRepository $closureRepository
    ): Response {
        $session = $cashRegisterSessionRepository->findAnyOpenSession();
        // dd($session);


        // 📤 CASH MOVEMENT FORM
        $cashMovement = new CashMovement();
        $cashMovementForm = $this->createForm(CashMovementType::class, $cashMovement);
        $cashMovementForm->handleRequest($request);

        if ($cashMovementForm->isSubmitted() && $cashMovementForm->isValid()) {
            $cashMovement->setCashRegisterSession($session);
            $cashMovement->setCreatedAt(new \DateTimeImmutable());
            $cashMovement->setMadeBy($user);

            $entityManager->persist($cashMovement);
            $entityManager->flush();

            $this->addFlash('success', 'Mouvement de caisse enregistré.');
            return $this->redirectToRoute('app_dashboard_cash_register');
        }

        $cashMovements = $session ? $session->getCashMovements()->toArray() : [];

        $sales = $session?->getSales()->toArray() ?? [];

        $totalCashMovements = 0;
        foreach ($cashMovements as $movement) {
            $amount = (float) $movement->getAmount();
            if ($movement->getType() === CashMovementAction::Deposit) {
                $totalCashMovements += $amount;
            } else {
                $totalCashMovements -= $amount;
            }
        }



        $totalPrice = $totalCard = $totalCash = 0;
        foreach ($sales as $sale) {
            $totalPrice += (float) $sale->getTotalPrice() + (float) $sale->getPwywAmount() + (float) $sale->getKeepChange();
            $totalCard += (float) $sale->getCardAmount();
            $totalCash += (float) $sale->getCashAmount();
        }

        $cashFloat = $session ? $session->getCashFloat() : 0;

        // $theoreticalBalance = ($cashFloat + $totalCash) - $totalWithdrawals;
        $theoreticalBalance = $cashFloat + $totalCash + $totalCashMovements;



        // 🧾 CASH REGISTER CLOSURE FORM 
        $cashRegisterClosure = new CashRegisterClosure();
        $closureForm = $this->createForm(CashRegisterClosureType::class, $cashRegisterClosure);
        $closureForm->handleRequest($request);

        if ($closureForm->isSubmitted() && $closureForm->isValid()) {
            // Récupérer la valeur du solde compté à partir du formulaire
            $countedBalance = $closureForm->get('countedBalance')->getData();

            //Récupère le montant de l'écart (positif ou négatif)
            $discrepancy = $cashRegisterClosure->getDiscrepancy();

            // Calcule le montant en espèces déposer à la banque
            $closingCashAmount = $countedBalance - $cashFloat;

            $cashRegisterClosure->setDiscrepancy($discrepancy);
            $cashRegisterClosure->setNote($closureForm->get('note')->getData());
            $cashRegisterClosure->setClosedBy($user);
            $cashRegisterClosure->setClosedAt(new \DateTimeImmutable());
            $cashRegisterClosure->setCashRegisterSession($session);
            $cashRegisterClosure->setClosingCashAmount($closingCashAmount);

            $entityManager->persist($cashRegisterClosure);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_cash_register');
        }


        //💰 LAST CLOSURE
        $lastClosure = $closureRepository->findLastClosureWithUser();


        return $this->render('dashboard/cash_register.html.twig', [
            'session' => $session,
            'cash_movement_form' => $cashMovementForm,
            'closure_form' => $closureForm,
            // 'opened_by' => $session?->getOpenedBy()?->getFirstName(),
            // 'opening_at' => $session?->getOpeningAt()?->format('d/m/Y à H\hi'),
            'cash_float' => $cashFloat,
            'total_price' => $totalPrice,
            'total_card' => $totalCard,
            'total_cash' => $totalCash,
            // 'total_withdrawals' => $totalWithdrawals,
            'lastClosure' => $lastClosure,
            'theoretical_balance' => $theoreticalBalance,
        ]);
    }
}
