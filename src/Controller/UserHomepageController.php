<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserHomepageController extends AbstractController
{
    #[Route('/user/homepage', name: 'app_user_homepage')]
    public function index(): Response
    {
        return $this->render('user_homepage/index.html.twig', [
            'controller_name' => 'UserHomepageController',
        ]);
    }
}
