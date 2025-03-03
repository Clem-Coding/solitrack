<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Donation;
use App\Form\DonationFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[IsGranted('IS_AUTHENTICATED')]
final class EntryController extends AbstractController
{
    #[Route('/entrees', name: 'app_entry', methods: ['GET', 'POST'])]
    public function index(#[CurrentUser] User $user, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {

        $donation = new Donation();

        $form = $this->createForm(DonationFormType::class, $donation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $categoryId = (int) $categoryId;

            $category = $categoryRepository->find($categoryId);


            $donation->setCategory($category);
            $donation->setUser($user);
            $donation->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($donation);
            $entityManager->flush();


            return $this->redirectToRoute('app_entry');
        }


        return $this->render('entry/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
