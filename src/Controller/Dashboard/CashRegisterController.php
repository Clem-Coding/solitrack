<?php

namespace App\Controller\Dashboard;

use App\Entity\CashRegisterClosure;
use App\Entity\CashRegisterSession;
use App\Entity\User;
use App\Entity\Withdrawal;
use App\Form\CashRegisterClosureType;
use App\Form\CoinCountType;
use App\Form\WithdrawalType;
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
        EntityManagerInterface $entityManager
    ): Response {
        $session = $cashRegisterSessionRepository->findAnyOpenSession();


        // 📤 Withdrawal form
        $withdrawal = new Withdrawal();
        $withdrawalForm = $this->createForm(WithdrawalType::class, $withdrawal);
        $withdrawalForm->handleRequest($request);

        if ($withdrawalForm->isSubmitted() && $withdrawalForm->isValid()) {
            $withdrawal->setCashRegisterSession($session);
            $withdrawal->setCreatedAt(new \DateTimeImmutable());
            $withdrawal->setMadeBy($user);

            $entityManager->persist($withdrawal);
            $entityManager->flush();

            $this->addFlash('success', 'Retrait enregistré.');
            return $this->redirectToRoute('app_dashboard_cash_register');
        }

        $withdrawals = $session ? $session->getWithdrawals()->toArray() : [];


        $totalWithdrawals = array_sum(array_map(fn($w) => (float) $w->getAmount(), $withdrawals));
        $sales = $session?->getSales()->toArray() ?? [];


        $totalPrice = $totalCard = $totalCash = 0;
        foreach ($sales as $sale) {
            $totalPrice += (float) $sale->getTotalPrice() + (float) $sale->getPwywAmount() + (float) $sale->getKeepChange();
            $totalCard += (float) $sale->getCardAmount();
            $totalCash += (float) $sale->getCashAmount();
        }

        $cashFloat = $session ? $session->getCashFloat() : 0;

        $theoreticalBalance = ($cashFloat + $totalCash) - $totalWithdrawals;




        // 🪙 Coins Count form 
        $coinsCountForm = $this->createForm(CoinCountType::class);

        // 🧾 Cash register Closure form 
        $cashRegisterClosure = new CashRegisterClosure();
        $closureForm = $this->createForm(CashRegisterClosureType::class, $cashRegisterClosure);
        $closureForm->handleRequest($request);

        if ($closureForm->isSubmitted() && $closureForm->isValid()) {
            $discrepancy = $cashRegisterClosure->getDiscrepancy();
            $countedTotal = $theoreticalBalance + $discrepancy;

            $cashRegisterClosure->setClosedBy($user);
            $cashRegisterClosure->setClosedAt(new \DateTimeImmutable());
            $cashRegisterClosure->setCashRegisterSession($session);
            $cashRegisterClosure->setClosingCashAmount($countedTotal);

            $entityManager->persist($cashRegisterClosure);
            $entityManager->flush();

            $this->addFlash('success', 'Clôture de caisse enregistrée avec succès.');
            return $this->redirectToRoute('app_dashboard_cash_register');
        }






        return $this->render('dashboard/cash_register.html.twig', [
            'session' => $session,
            'withdrawal_form' => $withdrawalForm,
            'coins_count_form' => $coinsCountForm,
            'closure_form' => $closureForm,
            'opened_by' => $session?->getOpenedBy()?->getFirstName(),
            'opening_at' => $session?->getOpeningAt()?->format('d/m/Y à H\hi'),
            'cash_float' => $cashFloat,
            'total_price' => $totalPrice,
            'total_card' => $totalCard,
            'total_cash' => $totalCash,
            'total_withdrawals' => $totalWithdrawals,
            'theoretical_balance' => $theoreticalBalance,
        ]);
    }




    public function closeCashRegisterSession(
        #[CurrentUser] User $user,
        Request $request,
        CashRegisterSessionRepository $sessionRepo,
        CashRegisterClosureRepository   $closureRepo,
        EntityManagerInterface $entityManager,

    ): Response {
        $session = $sessionRepo->findAnyOpenSession();

        $form = $this->createForm(CashRegisterClosureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dd($data);
            // Traitement logique ici
        }





        $this->addFlash('success', 'Clôture enregistrée avec succès.');

        return $this->redirectToRoute('app_sales');
    }
}



  // $closure = new CashRegisterClosure();
        // $closure->setCashRegisterSession($session);
        // $closure->setClosedBy($user);
        // $closure->setClosedAt(new \DateTimeImmutable());
        // $closure->setClosingCashAmount($closingCashAmount);
        // $closure->setDiscrepancy($discrepancy);
        // $closure->setNote($request->request->get('note', ''));

        // $entityManager->persist($closure);
        // $entityManager->flush();