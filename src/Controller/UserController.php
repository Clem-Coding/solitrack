<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserAccountType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;


use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\UserType;


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
    public function editAccount(Request $request, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {



        /** @var \App\Entity\User $user */     //précise que c'est une instance User
        $user = $this->getUser();


        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $userRepository->upgradePassword($user, $hashedPassword);


            // if (!empty($plainPassword)) {

            //     $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            // }


            // $manager->flush();



            // Redirection après la soumission du formulaire
            return $this->redirectToRoute('app_user_account');
        }

        return $this->render('user/account.html.twig', [
            'form' => $form,
        ]);
    }
}
