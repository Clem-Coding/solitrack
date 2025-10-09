<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\VolunteerRegistration;
use App\Entity\VolunteerSession;
use App\Repository\VolunteerRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;



#[Route('/mon-compte/benevolat')]
final class VolunteerRegistrationController extends AbstractController
{
    #[Route('/', name: 'app_user_benevolat')]
    public function index(#[CurrentUser] User $user, VolunteerRegistrationRepository $registrationRepo): Response
    {
        $upcomingRegistrations = $registrationRepo->findUpcomingSessionsByUser($user);

        $isRegistered = false;

        return $this->render('user/volunteer_registration.html.twig', [
            'upcoming_registrations' => $upcomingRegistrations,
            'is_registered' => $isRegistered,

        ]);
    }

    #[Route('/sessions', name: 'app_user_benevolat_sessions')]
    public function getSessions(EntityManagerInterface $em): JsonResponse
    {
        $sessions = $em->getRepository(VolunteerSession::class)->findBy(['isCancelled' => false]);

        $data = array_map(function ($s) {
            $firstNames = [];
            $volunteerIds = [];

            foreach ($s->getVolunteerRegistrations() as $registration) {
                if (!in_array($registration->getStatus(), ['cancelled_by_user', 'cancelled_by_admin'])) {
                    $user = $registration->getUser();
                    if ($user) {
                        $firstNames[] = $user->getFirstName();
                        $volunteerIds[] = $user->getId();
                    }
                }
            }

            $registeredCount = count($firstNames);
            $requiredCount = $s->getRequiredVolunteers();


            return [
                'title' => $s->getTitle(),
                'start' => $s->getStartDatetime()->format(DATE_ATOM),
                'end' => $s->getEndDatetime()->format(DATE_ATOM),
                'id' => $s->getId(),
                'frequency' => $s->getRecurrence()?->getFrequency(),
                'description' => $s->getDescription(),
                'location' => $s->getLocation(),
                'until' => $s->getRecurrence()?->getUntilDate()?->format('Y-m-d'),
                'registeredVolunteers' => $registeredCount,
                'requiredVolunteers' => $requiredCount,
                'volunteerFirstNames' => $firstNames,
                "volunteerIds" => $volunteerIds,

            ];
        }, $sessions);

        return $this->json($data);
    }

    #[Route('/sessions/register/{id}', name: 'app_user_benevolat_register', methods: ['POST'])]
    public function registerForSession(int $id, EntityManagerInterface $em, #[CurrentUser] User $user): JsonResponse
    {
        $session = $em->getRepository(VolunteerSession::class)->find($id);

        if (!$session) {
            return $this->json(['success' => false, 'message' => 'Session non trouvée'], 404);
        }

        $registrationRepo = $em->getRepository(VolunteerRegistration::class);
        $registration = $registrationRepo->findOneBy(['session' => $session, 'user' => $user]);

        if ($registration) {
            // Si l'inscription existe déjà, on la réactive
            if (in_array($registration->getStatus(), ['cancelled_by_user', 'cancelled_by_admin'])) {
                $registration->setStatus('registered');
                $registration->setUpdatedAt(new \DateTimeImmutable());
                $em->flush();

                return $this->json(['success' => true, 'message' => 'Inscription réactivée']);
            }
            return $this->json(['success' => false, 'message' => 'Déjà inscrit à cette session'], 400);
        }

        // Crée une nouvelle inscription
        $registration = new VolunteerRegistration();
        $registration->setSession($session);
        $registration->setUser($user);
        $registration->setStatus('registered');
        $registration->setRegisteredAt(new \DateTimeImmutable());
        $registration->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($registration);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Inscription réussie']);
    }


    #[Route('/sessions/unsubscribe/{sessionId}', name: 'app_volunteer_registration_cancel')]
    public function cancelRegistration(int $sessionId, EntityManagerInterface $em, #[CurrentUser] User $user, VolunteerRegistrationRepository $registrationRepository): JsonResponse
    {

        $session = $em->getRepository(VolunteerSession::class)->find($sessionId);

        if (!$session) {
            return $this->json(['success' => false, 'message' => 'Session non trouvée'], 404);
        }

        $registration = $registrationRepository->findRegistrationBySessionAndUser($session, $user);

        if (!$registration) {
            return $this->json(['success' => false, 'message' => 'Aucune inscription trouvée'], 404);
        }

        if ($registration && $registration->getUser() === $user) {
            $registration->setStatus('cancelled_by_user');
            $registration->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return $this->json(['success' => true, 'message' => 'Désinscription réussie']);
        }

        return new JsonResponse(['success' => false, 'message' => 'Inscription non trouvée ou accès refusé.'], 404);
    }
}
