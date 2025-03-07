<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Donation;
use App\Form\DonationFormType;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
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
    public function index(#[CurrentUser] User $user, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, DonationRepository $donationRepository): Response
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

        $lastEntry = $donationRepository->getLatestEntry();
        // dd($lastEntry);

        $lastEntryName = $lastEntry['categoryName'] ?? null;
        $lastEntryWeight = $lastEntry['weight'] ?? null;



        return $this->render('entry/index.html.twig', [
            'form' => $form,
            'lastEntryName' => $lastEntryName,
            'lastEntryWeight' => $lastEntryWeight,
        ]);
    }

    // #[Route('/entrees/show-last', name: 'app_entry_show_last', methods: ['GET'])]
    // public function showLastEntry(Request $request, DonationRepository $donationRepository, EntityManagerInterface $manager): Response
    // {
    //     $lastEntry = $donationRepository->getLatestEntry();

    //     return $this->render('entry/index.html.twig', [

    //         'lastEntryName' => $lastEntry['categoryName'],
    //         'lastEntryWeight' => $lastEntry['weight'],
    //     ]);
    // }


    // $totalWeightToday = $donationRepository->getTotalWeightForToday();
    // 'totalWeightToday' => $totalWeightToday

    #[Route('/entrees/delete-last', name: 'app_entry_delete_last', methods: ['DELETE'])]
    public function deleteLastEntry(DonationRepository $donationRepository, EntityManagerInterface $entityManager): Response
    {
        $lastEntry = $donationRepository->getLatestEntry();

        if ($lastEntry === null) {

            $this->addFlash('erreur', 'Aucune donnée enregistrée en BDD.');
        } else {
            if (isset($lastEntry['id'])) {
                $donation = $donationRepository->find($lastEntry['id']);
                if ($donation) {
                    $entityManager->remove($donation);
                    $entityManager->flush();
                    $this->addFlash('succès', 'Votre dernière entrée a bien été supprimée.');
                } else {
                    $this->addFlash('erreur', 'Entrée introuvable.');
                }
            } else {
                $this->addFlash('erreur', 'Aucune entrée à supprimer.');
            }
        }

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
