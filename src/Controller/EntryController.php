<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Donation;
use App\Form\DonationFormType;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
use App\Service\FeedbackMessagesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[IsGranted('IS_AUTHENTICATED')]
final class EntryController extends AbstractController
{
    #[Route('/entrees', name: 'app_entry', methods: ['GET', 'POST'])]
    public function index(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        DonationRepository $donationRepository,
        FeedbackMessagesService $feedbackMessage,
        SessionInterface $session
    ): Response {

        $donation = new Donation();

        $form = $this->createForm(DonationFormType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryId = $form->get('categoryId')->getData();
            $category = $categoryRepository->find($categoryId);

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
        $lastEntryWeight = number_format($lastEntry['weight'], 2, ',', ' ') ?? null;
        $recordWeight = $donationRepository->getRecordWeightDay()['total_weight'];

        $isRecordJustBeaten = false;

        $feedback = $feedbackMessage->getRandomFeedbackMessage();

        $isRecordJustBeaten = false;
        if ($feedback['totalWeight'] >= $recordWeight && !$session->get('recordBeaten', false)) {
            $isRecordJustBeaten = true;
            $session->set('recordBeaten', true);
        }

        return $this->render('entry/index.html.twig', [
            'form' => $form,
            'lastEntryName' => $lastEntryName,
            'lastEntryWeight' => $lastEntryWeight,
            'totalWeightToday' => $feedback['totalWeight'],
            'feedbackMessage' => $feedback['message'],
            'record_weight' => $recordWeight,
            'is_record_just_beaten' => $isRecordJustBeaten
        ]);
    }



    // In Symfony, Doctrine ORM manages the deletion of entities via the EntityManager. 
    // This ensures the proper removal of objects from the database, handling relationships and entity state automatically.

    // an equivalent MySQL query would be:

    // DELETE 
    // FROM donations 
    // WHERE id = {lastEntryId};

    #[Route('/entrees/delete-last', name: 'app_entry_delete_last', methods: ['DELETE'])]
    public function deleteLastEntry(
        DonationRepository $donationRepository,
        EntityManagerInterface $entityManager,
        FeedbackMessagesService $feedbackMessage,
        SessionInterface $session
    ): Response {

        $lastEntry = $donationRepository->getLatestEntry();

        if ($lastEntry && isset($lastEntry['id'])) {
            $donation = $donationRepository->find($lastEntry['id']);
            if ($donation) {
                $entityManager->remove($donation);
                $entityManager->flush();
            }
        }

        $feedback = $feedbackMessage->getRandomFeedbackMessage();
        $totalWeightToday = $feedback['totalWeight'];
        $recordWeight = $donationRepository->getRecordWeightDay()['total_weight'];

        if ($totalWeightToday < $recordWeight) {
            $session->set('recordBeaten', false);
        }

        $this->addFlash('success', 'Votre dernière entrée a bien été supprimée.');

        return $this->redirectToRoute('app_entry');
    }
}
