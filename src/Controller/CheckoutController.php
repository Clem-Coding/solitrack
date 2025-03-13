<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SalesItem;
use App\Entity\Sale;
use App\Entity\Category;
use App\Service\PriceManagement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CheckoutController extends AbstractController
{
    #[Route('/ventes/caisse', name: 'app_sale_checkout')]
    public function index(SessionInterface $session, PriceManagement $priceManagement): Response
    {
        $shoppingCart = $session->get('shopping_cart', []);


        if (empty($shoppingCart)) {
            $this->addFlash('error', 'Votre panier est vide. Veuillez ajouter des articles avant de passer à la caisse.');


            return $this->redirectToRoute('app_sales');
        }


        return $this->render('sales/checkout.html.twig', [
            'shopping_cart' => $shoppingCart,
            'total' => $priceManagement->getTotal(),
        ]);
    }


    #[Route('/ventes/caisse/register', name: 'app_sale_register')]
    public function registerSale(#[CurrentUser] User $user, SessionInterface $session, EntityManagerInterface $entityManager)
    {

        $shoppingCart = $session->get('shopping_cart', []);


        // à laisser au cas où, mais empeecher de passer à la caisse si le panier est vide
        if (empty($shoppingCart)) {

            return $this->redirectToRoute('app_sales');
        }


        $sale = new Sale();
        $sale->setCreatedAt(new \DateTimeImmutable());
        $sale->setUser($user);

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


        // $entityManager->persist($salesItem);

        $sale->setTotalPrice($totalPrice);
        // dd($sale);
        $entityManager->persist($sale);
        $entityManager->flush();

        $session->remove('shopping_cart');

        return $this->redirectToRoute('app_sales');
    }
}
