<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Form\SalesItemType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class SalesController extends AbstractController
{
    #[Route('/ventes', name: 'app_sales', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {

        $salesItem = new SalesItem();

        $form = $this->createForm(SalesItemType::class, $salesItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $category = $categoryRepository->find($categoryId);
            $salesItem->setCategory($category);


            // On crée le panier ici
            $request->getSession()->set('sales_cart', [
                'category' => $category->getName(),
                'weight' => $salesItem->getWeight(),
                'price' => $salesItem->getPrice(),
                'quantity' => $salesItem->getQuantity(),
            ]);




            $this->addFlash('success', 'Article ajouté au panier !');

            // On redirige vers le cart controller où on effectura 
            return $this->redirectToRoute('app_cart_add');
        }

        return $this->render('sales/index.html.twig', [
            'form' => $form,
        ]);
    }
}




 // return $this->redirectToRoute('app_cart_add', [
            //     'category' => $salesItem->getCategory(),
            //     'weight' => $salesItem->getWeight(),
            //     'price' => $salesItem->getPrice(),
            //     'quantity' => $salesItem->getQuantity(),
            // ]);
