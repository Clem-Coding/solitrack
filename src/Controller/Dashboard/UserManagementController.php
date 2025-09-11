<?php

namespace App\Controller\Dashboard;

use App\Entity\Sale;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;


class UserManagementController extends AbstractController
{

    #[Route('/tableau-de-bord/gestion-utilisateurs', name: 'app_dashboard_user_management')]
    public function userManagement(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('dashboard/user_management.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/tableau-de-bord/gestion-utilisateurs/{id}/role', name: 'app_user_role')]
    public function assignRole(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            $newRole = $request->get('role');

            if ($newRole) {
                $user->setRoles([$newRole]);
                $entityManager->flush();
                $this->addFlash('success', 'Rôle attribué avec succès.');
            }
        } else {
            $this->addFlash('error', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('app_dashboard_user_management');
    }


    #[Route('/tableau-de-bord/gestion-utilisateurs/{id}/supprimer', name: 'app_user_delete')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_dashboard_user_management');
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Impossible de supprimer un super-admin.');
            return $this->redirectToRoute('app_dashboard_user_management');
        }

        $sales = $entityManager->getRepository(Sale::class)->findBy(['user' => $user]);
        foreach ($sales as $sale) {
            $sale->setUser(null);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_dashboard_user_management');
    }
}
