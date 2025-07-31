<?php

namespace App\Controller;

use App\Entity\CashRegisterSession;
use App\Entity\SalesItem;
use App\Entity\User;
use App\Form\CashFloatType;
use App\Form\SalesItemType;
use App\Repository\CashRegisterSessionRepository;
use App\Service\PriceManagementService;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;


#[IsGranted('IS_AUTHENTICATED')]
final class SalesController extends AbstractController

{
    #[Route('/ventes', name: 'app_sales', methods: ['GET', 'POST'])]
    public function index(
        #[CurrentUser] User $user,
        Request $request,
        SessionInterface $session,
        PriceManagementService $priceManagement,
        CashRegisterSessionRepository $cashRegisterSessionRepository,
        EntityManagerInterface $entityManager,
    ): Response {

        $openSession = $cashRegisterSessionRepository->findAnyOpenSession();
        if (!$openSession) {
            $cashRegisterSession = new CashRegisterSession();
            $form = $this->createForm(CashFloatType::class, $cashRegisterSession);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $cashRegisterSession->setOpenedBy($user);
                $cashRegisterSession->setOpeningAt(new \DateTimeImmutable());

                $entityManager->persist($cashRegisterSession);
                $entityManager->flush();

                return $this->redirectToRoute('app_sales');
            }

            return $this->render('sales/open_cash_register.html.twig', [
                'form' => $form->createView(),
            ]);
        }



        $salesItem = new SalesItem();

        $form = $this->createForm(SalesItemType::class, $salesItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $salesItem->getCategory();
            $priceManagement->setDrinkPrice($salesItem);
            $priceManagement->setWeightBasedPrice($salesItem);
            $uuid = Uuid::v4();
            $shoppingCart = $session->get('shopping_cart', []);

            $shoppingCart[] = [
                'uuid' => $uuid->toString(),
                'category' => $category->getName(),
                'weight' => $salesItem->getWeight(),
                'price' => $salesItem->getPrice(),
                'quantity' => $salesItem->getQuantity(),
                'id' =>  $category->getId(),
            ];

            $session->set('shopping_cart', $shoppingCart);

            $total = $priceManagement->getCartTotal();

            return $this->json([
                'status' => 'success',
                'cart' => $shoppingCart ?? [],
                'total' => $total,
            ]);
        }

        return $this->render('sales/index.html.twig', [
            'form' => $form,
            'total' => $priceManagement->getCartTotal()
        ]);
    }
}
