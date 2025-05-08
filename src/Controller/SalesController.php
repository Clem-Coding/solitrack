<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Form\SalesItemType;
use App\Repository\CategoryRepository;
use App\Repository\SalesItemRepository;
use App\Service\PriceManagement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[IsGranted('IS_AUTHENTICATED')]
final class SalesController extends AbstractController

{
    #[Route('/ventes', name: 'app_sales', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        SessionInterface $session,
        PriceManagement $priceManagement
    ): Response {

        $salesItem = new SalesItem();

        $form = $this->createForm(SalesItemType::class, $salesItem);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $category = $categoryRepository->find($categoryId);
            $salesItem->setCategory($category);

            $priceManagement->setDrinkPrice($salesItem);
            $priceManagement->setWeightBasedPrice($salesItem);

            $uuid = Uuid::v1();
            $shoppingCart = $session->get('shopping_cart', []);

            if ($category && $category->getId() == 4) {
                $quantity = $salesItem->getQuantity();
            } else {
                $quantity = null;
            }

            $shoppingCart[] = [
                'uuid' => $uuid->toString(),
                'category' => $category->getName(),
                'weight' => $salesItem->getWeight(),
                'price' => $salesItem->getPrice(),
                'quantity' => $quantity
            ];


            $session->set('shopping_cart', $shoppingCart);




            //à revoir XmLhttpRequest , supprimer?
            if ($request->isXmlHttpRequest()) {
                $total = $priceManagement->getCartTotal();
                return $this->json([
                    'status' => 'success',
                    'cart' => $shoppingCart ?? [],
                    'total' => $total,
                ]);
            }

            $this->addFlash('success', 'Article ajouté au panier !');

            return $this->redirectToRoute('app_sales');
        }



        return $this->render('sales/index.html.twig', [
            'form' => $form,
            'total' => $priceManagement->getCartTotal()
        ]);
    }
}
