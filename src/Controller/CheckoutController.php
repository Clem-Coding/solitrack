<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SalesItem;
use App\Entity\Sale;
use App\Entity\Category;
use App\Form\SaleType;
use App\Service\PriceManagement;
use App\Service\ReceiptMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;


class CheckoutController extends AbstractController
{
    #[Route('/ventes/caisse', name: 'app_sale_checkout')]
    public function index(SessionInterface $session, PriceManagement $priceManagement): Response
    {


        $form = $this->createForm(SaleType::class);

        // $shoppingCart = $session->get('shopping_cart', []);
        $priceManagement->applyBulkPricingRule();
        $shoppingCart = $session->get('shopping_cart');



        if (empty($shoppingCart)) {
            $this->addFlash('error', 'Votre panier est vide. Veuillez ajouter des articles avant de passer à la caisse.');

            return $this->redirectToRoute('app_sales');
        }


        return $this->render('sales/checkout.html.twig', [
            'form' => $form,
            'shopping_cart' => $shoppingCart,
            'total' => $priceManagement->getCartTotal(),
        ]);
    }


    #[Route('/ventes/caisse/register', name: 'app_sale_register')]
    public function registerSale(
        #[CurrentUser] User $user,
        Request $request,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        ReceiptMailer $receiptMailer
    ): Response {

        $token = $request->request->get('_csrf_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('app_sale_register', $token))) {
            throw new \InvalidArgumentException('Invalid CSRF token');
        }


        $sale = new Sale();

        // $form = $this->createForm(SaleType::class);

        // $form->handleRequest($request);;
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $zipcode =  $form->get('zipcodeCustomer')->getData();
        //     $sale->setZipcodeCustomer($zipcode ?? null);
        // }


        $cardAmount = $request->get('card_amount');
        $cashAmount = $request->get('cash_amount');
        $keepChangeAmount = $request->get('keep_change');
        $pwywAmount = $request->get("pwyw_amount");
        $pwywAmount = str_replace(',', '.', $pwywAmount);
        // dd($pwywAmount);
        $zipcode = $request->get("zipcode");
        $shoppingCart = $session->get('shopping_cart', []);



        $to = $request->get('email');

        if (!empty($to)) {
            $receiptMailer->sendReceipt($to);
        }



        // à laisser au cas où, mais empecher de passer à la caisse si le panier est vide
        if (empty($shoppingCart)) {

            return $this->redirectToRoute('app_sales');
        }


        $sale->setCreatedAt(new \DateTimeImmutable());
        $sale->setUser($user);
        $sale->setCardAmount($cardAmount ?? null);
        $sale->setCashAmount($cashAmount ?? null);
        $sale->setKeepChange($keepChangeAmount) ?? null;
        $sale->setPWYWAmount($pwywAmount) ?? null;
        $sale->setZipcodeCustomer($zipcode) ?? null;

        $totalPrice = 0;

        foreach ($shoppingCart as $itemData) {
            $salesItem = new SalesItem();
            $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $itemData['category']]);


            if ($category) {
                $salesItem->setCategory($category);
            } else {

                $salesItem->setCategory(null);
            }


            $salesItem->setWeight($itemData['weight'] ?? null);
            $salesItem->setPrice($itemData['price'] ?? null);
            $salesItem->setQuantity($itemData['quantity'] ?? null);

            $sale->addSalesItem($salesItem);

            $itemPrice = $salesItem->getPrice();

            //A faire : if tip condition pour ne pas ajouter le poids si prix libre donné
            if ($itemPrice !== null) {
                $totalPrice += $itemPrice;
            }
            $entityManager->persist($salesItem);
        }


        $sale->setTotalPrice($totalPrice);

        $entityManager->persist($sale);
        $entityManager->flush();
        $session->remove('shopping_cart');
        $this->addFlash('success', 'Vente enregistrée avec succès.');


        return $this->redirectToRoute('app_sales', ['clear_local_storage' => true]);
    }
}
