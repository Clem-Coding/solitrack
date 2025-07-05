<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserAccountType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;



// use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\UserType;
use App\Repository\DonationRepository;
use App\Repository\SalesItemRepository;
use App\Service\MonthService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class UserController extends AbstractController
{
    #[Route('/accueil', name: 'app_user_homepage')]
    public function index(MonthService $monthService, DonationRepository $donationRepository, SalesItemRepository $salesItemRepository): Response
    {


        $entryWeight = $donationRepository->findTotalWeightForCurrentMonth();
        $outWeight = $salesItemRepository->findTotalWeightForCurrentMonth();


        $currentMonth = date('n');
        $statsTitle = $monthService->getMonthStatsTitle($currentMonth);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserHomepageController',
            'stats_title' => $statsTitle,
            'entry_weight' => $entryWeight,
            'out_weight' => $outWeight,
        ]);
    }

    #[Route('/mon-compte', name: 'app_user_account', methods: ['GET', 'POST'])]
    public function editAccount(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $manager,
    ): Response {


        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // The $user object is already managed by Doctrine (injected via #[CurrentUser]),
            // so Doctrine tracks changes on the $user object and generates an UPDATE query on flush().

            // Exemple of conversion in mySQL query :

            // UPDATE user 
            // SET last_name = 'Durand' 
            // WHERE id = 42;


            $manager->flush();

            $this->addFlash('success', 'Vos informations ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_user_account');
        }

        return $this->render('user/account.html.twig', [
            'form' => $form,
        ]);
    }
}
