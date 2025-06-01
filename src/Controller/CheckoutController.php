<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SalesItem;
use App\Entity\Sale;
use App\Entity\Category;
use App\Form\SaleType;
use App\Service\PriceManagementService;
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
    public function index(SessionInterface $session, PriceManagementService $priceManagement): Response
    {


        $form = $this->createForm(SaleType::class);
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
        ReceiptMailer $receiptMailer,
        PriceManagementService $priceManagement
    ): Response {

        $token = $request->request->get('_csrf_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('app_sale_register', $token))) {
            throw new \InvalidArgumentException('Invalid CSRF token');
        }


        $sale = new Sale();

        $cardAmounts = $request->get('card_amount', []);
        $cashAmounts = $request->get('cash_amount', []);
        $cardTotal = array_sum(array_map('floatval', $cardAmounts));
        $cashTotal = array_sum(array_map('floatval', $cashAmounts));

        $keepChangeAmount = $request->get('keep_change');
        $pwywAmount = $request->get("pwyw_amount");
        $pwywAmount = str_replace(',', '.', $pwywAmount);
        $zipcode = $request->get("zipcode");
        $customerCity = $request->get('city');
        $shoppingCart = $session->get('shopping_cart', []);

        $to = $request->get('email');
        if (!empty($to)) {
            $receiptMailer->sendReceipt($to);
        }

        if (empty($shoppingCart)) {
            return $this->redirectToRoute('app_sales');
        }

        $totalPrice = $priceManagement->getCartTotal();

        $sale->setCreatedAt(new \DateTimeImmutable());
        $sale->setUser($user);
        $sale->setCardAmount($cardTotal ?? null);
        $sale->setCashAmount($cashTotal ?? null);
        $sale->setKeepChange($keepChangeAmount) ?? null;
        $sale->setPWYWAmount($pwywAmount) ?? null;
        $sale->setZipcodeCustomer($zipcode) ?? null;
        $sale->setTotalPrice($totalPrice);
        $sale->setCustomerCity($customerCity ?? null);

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
            $entityManager->persist($salesItem);
        }

        $entityManager->persist($sale);
        $entityManager->flush();
        $session->remove('shopping_cart');
        $this->addFlash('success', 'Vente enregistrée avec succès.');

        return $this->redirectToRoute('app_sales', ['clear_local_storage' => true]);
    }
}
