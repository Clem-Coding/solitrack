<?php

// src/Controller/BlablaController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Forms;

class BlablaController extends AbstractController
{
    #[Route('/test_form', name: 'test_form')]
    public function testForm(Request $request): Response
    {
        // Création du formulaire
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            // Effectuer des actions avec les données (ex. les afficher)
            return new Response('Form submitted with name: '.$data['name']);
        }

        // Afficher le formulaire
        return $this->render('blabla/test_form.html.twig', [
            'form' => $form
        ]);
    }
}
