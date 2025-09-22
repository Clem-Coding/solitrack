<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\VolunteerRegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class VolunteerRegistrationController extends AbstractController
{
    #[Route('/mon-compte/benevolat', name: 'app_account_benevolat')]
    public function index(#[CurrentUser] User $user, VolunteerRegistrationRepository $registrationRepo): Response
    {
        $upcomingRegistrations = $registrationRepo->findUpcomingSessionsByUser($user);
        // dd($upcomingRegistrations);

        return $this->render('user/volunteer_schedule.html.twig', [
            'upcoming_registrations' => $upcomingRegistrations,
        ]);
    }

    // #[Route('/volunteer/registration/cancel/{id}', name: 'app_volunteer_registration_cancel')]
    // public function cancelRegistration(int $id, EntityManagerInterface $em, #[CurrentUser] User $user): Response
    // {
    //     $registration = $em->getRepository(VolunteerRegistration::class)->find($id);

    //     if ($registration && $registration->getUser() === $user) {
    //         $registration->setStatus('canceled');
    //         $registration->setUpdatedAt(new \DateTimeImmutable());
    //         $em->flush();
    //     }

    //     return $this->redirectToRoute('app_volunteer_registration');
    // }
}
