<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Form\SalesItemType;
use App\Repository\CategoryRepository;
use App\Repository\SalesItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class SalesController extends AbstractController

{
    #[Route('/ventes', name: 'app_sales', methods: ['GET', 'POST'])]
    public function index(Request $request, CategoryRepository $categoryRepository, SessionInterface $session, SalesItemRepository $salesItemRepository): Response
    {

        $salesItem = new SalesItem();

        $form = $this->createForm(SalesItemType::class, $salesItem);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $category = $categoryRepository->find($categoryId);
            $salesItem->setCategory($category);

            // Récupérer les éléments existants dans le panier
            $salesCart = $session->get('sales_cart', []);
            $salesCart[] = $salesItem;
            $session->set('sales_cart', $salesCart);



            $this->addFlash('success', 'Article ajouté au panier !');


            return $this->redirectToRoute('app_cart_add');
        }

        return $this->render('sales/index.html.twig', [
            'form' => $form,
        ]);
    }
}
