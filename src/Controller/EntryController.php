<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Donation;
use App\Form\DonationFormType;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
use App\Service\FeedbackMessages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;


#[IsGranted('IS_AUTHENTICATED')]
final class EntryController extends AbstractController
{
    #[Route('/entrees', name: 'app_entry', methods: ['GET', 'POST'])]
    public function index(#[CurrentUser] User $user, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, DonationRepository $donationRepository, FeedbackMessages $feedbackMessage): Response
    {




        $donation = new Donation();

        $form = $this->createForm(DonationFormType::class, $donation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            // $categoryId = (int) $categoryId;
            $category = $categoryRepository->find($categoryId);


            if (empty($category)) {
                // $this->addFlash('warning', 'Veuillez sélectionner une catégorie.');
                // return $this->redirectToRoute('app_entry');
            }


            $totalWeightToday = $donationRepository->getTotalWeightForToday();
            if (!empty($category)) {
                $donation->setCategory($category);
            }

            $donation->setUser($user);
            $donation->setCreatedAt(new \DateTimeImmutable());


            //example MySql query equivalence
            // INSERT INTO donations (weight, created_at, user_id, category_id) VALUES(?, ?, ?, ?)

            $entityManager->persist($donation);
            $entityManager->flush();

            return $this->redirectToRoute('app_entry');
        }

        $lastEntry = $donationRepository->getLatestEntry();
        $lastEntryName = $lastEntry['categoryName'] ?? null;
        $lastEntryWeight = $lastEntry['weight'] ?? null;



        $feedback = $feedbackMessage->getRandomFeedbackMessage();


        return $this->render('entry/index.html.twig', [
            'form' => $form,
            'lastEntryName' => $lastEntryName,
            'lastEntryWeight' => $lastEntryWeight,
            'totalWeightToday' => $feedback['totalWeight'],
            'feedbackMessage' => $feedback['message']
        ]);
    }



    // In Symfony, Doctrine ORM manages the deletion of entities via the EntityManager. 
    // This ensures the proper removal of objects from the database, handling relationships and entity state automatically.

    // an equivalent MySQL query would be:
    // DELETE FROM donations WHERE id = {lastEntryId};

    #[Route('/entrees/delete-last', name: 'app_entry_delete_last', methods: ['DELETE'])]
    public function deleteLastEntry(DonationRepository $donationRepository, EntityManagerInterface $entityManager): Response
    {
        $lastEntry = $donationRepository->getLatestEntry();

        if ($lastEntry && isset($lastEntry['id'])) {
            $donation = $donationRepository->find($lastEntry['id']);
            if ($donation) {
                $entityManager->remove($donation);
                $entityManager->flush();
            }
        }

        $this->addFlash('success', 'Votre dernière entrée a bien été supprimée.');

        return $this->redirectToRoute('app_entry');
    }



    // #[Route('/entrees/edit-last', name: 'app_entry_edit_last', methods: ['POST'])]
    // public function editLastEntry(DonationRepository $donationRepository, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     try {
    //         $lastEntry = $donationRepository->getLatestEntry();

    //         if ($lastEntry && isset($lastEntry['id'])) {
    //             $donation = $donationRepository->find($lastEntry['id']);
    //             $entityManager->remove($donation);
    //             $entityManager->flush();

    //             return new JsonResponse(['success' => true]);
    //         }

    //         return new JsonResponse(['success' => false, 'message' => 'Aucune entrée à supprimer.']);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['success' => false, 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
    //     }
    // }
}
