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
            // $categoryId = (int) $categoryId;
            $category = $categoryRepository->find($categoryId);
            dd($category);

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

            $entityManager->persist($donation);
            $entityManager->flush();

            return $this->redirectToRoute('app_entry');
        }

        $lastEntry = $donationRepository->getLatestEntry();
        $lastEntryName = $lastEntry['categoryName'] ?? null;
        $lastEntryWeight = $lastEntry['weight'] ?? null;

        $totalWeightToday = $donationRepository->getTotalWeightForToday();

        $messages = [
            "Chaque kilo compte : aujourd'hui, $totalWeightToday kg rentrés !",
            "$totalWeightToday kg, allez les fourmis, on lâche rien !",
            "Vous avez enregistré $totalWeightToday kg !",
            "Une belle collecte aujourd'hui : $totalWeightToday kg déjà !",
            "Ohlala $totalWeightToday kg! si ça continue comme ça, il va falloir pousser les murs !"
        ];

        $feedbackMessage = $messages[array_rand($messages)];


        return $this->render('entry/index.html.twig', [
            'form' => $form,
            'lastEntryName' => $lastEntryName,
            'lastEntryWeight' => $lastEntryWeight,
            'totalWeightToday' => $totalWeightToday,
            'feedbackMessage' => $feedbackMessage
        ]);
    }



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
