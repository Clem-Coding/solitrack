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
    #[Route('/ventes', name: 'app_sales')]
    public function index(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {

        $salesItem = new SalesItem();

        $form = $this->createForm(SalesItemType::class, $salesItem);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $categoryId = (int) $categoryId;
            $category = $categoryRepository->find($categoryId);

            if (empty($category)) {
                // $this->addFlash('warning', 'Veuillez sélectionner une catégorie.');
                // return $this->redirectToRoute('app_entry');
            }

            $entityManager->persist($salesItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_entry');
        }


        return $this->render('sales/index.html.twig', [
            'form' => $form,
        ]);
    }
}
