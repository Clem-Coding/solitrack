<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserAccountType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;



// use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\UserType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
final class UserController extends AbstractController
{
    #[Route('/accueil', name: 'app_user_homepage')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserHomepageController',
        ]);
    }

    #[Route('/mon-compte', name: 'app_user_account', methods: ['GET', 'POST'])]
    public function editAccount(Request $request,  EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Vos informations ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_user_account');
        }

        return $this->render('user/account.html.twig', [
            'form' => $form,
        ]);
    }
}
