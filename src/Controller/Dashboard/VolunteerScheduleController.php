<?php

namespace App\Controller\Dashboard;

use App\Entity\User;
use App\Entity\VolunteerRecurrence;
use App\Entity\VolunteerRegistration;
use App\Form\VolunteerSessionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\VolunteerSession;
use App\Form\VolunteerSessionEditType;
use App\Repository\VolunteerSessionRepository;
use App\Service\VolunteerSessionGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/tableau-de-bord/planning-benevolat')]
class VolunteerScheduleController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_volunteer_schedule')]
    public function index(): Response
    {
        $session = new VolunteerSession();

        $createForm = $this->createForm(VolunteerSessionType::class, $session);
        $editForm = $this->createForm(VolunteerSessionEditType::class, $session);


        return $this->render('dashboard/volunteer_schedule.html.twig', [
            'createForm' => $createForm,
            'editForm' => $editForm

        ]);
    }

    #[Route('/creer', name: 'app_dashboard_schedule_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
        VolunteerSessionGeneratorService $volunteerSessionGenerator
    ): JsonResponse {

        $session = new VolunteerSession();
        $form = $this->createForm(VolunteerSessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fromDate = $form->get('from_date')->getData();
            $fromTime = $form->get('from_time')->getData();
            $toDate = $form->get('to_date')->getData();
            $toTime = $form->get('to_time')->getData();
            $recurrenceValue = $form->get('recurrence')->getData();
            $untilDate = $form->get('until_date')->getData();


            $session->setStartDatetime(\DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $fromDate->format('Y-m-d') . ' ' . $fromTime->format('H:i')
            ));

            $session->setEndDatetime(\DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $toDate->format('Y-m-d') . ' ' . $toTime->format('H:i')
            ));

            $session->setIsCancelled(false);
            $session->setCreatedBy($user);
            $session->setCreatedAt(new \DateTimeImmutable());
            $session->setUpdatedAt(new \DateTimeImmutable());

            if ($recurrenceValue) {
                $recurrence = new VolunteerRecurrence();
                $recurrence->setFrequency($recurrenceValue);

                //A faire plus tard : changer le type en \DateTime
                if ($untilDate) {
                    $recurrence->setUntilDate(\DateTimeImmutable::createFromMutable($untilDate));
                }

                $recurrence->setCreatedAt(new \DateTimeImmutable());
                $recurrence->setUpdatedAt(new \DateTimeImmutable());

                $em->persist($recurrence);
                $session->setRecurrence($recurrence);

                $em->persist($session);
                $em->flush();

                $volunteerSessionGenerator->generateOccurrences($session);
            } else {
                $em->persist($session);
                $em->flush();
            }

            return new JsonResponse(['success' => true]);
        }
        return new JsonResponse(['success' => false, 'errors' => (string) $form->getErrors(true, false)], 400);
    }

    #[Route('/sessions', name: 'get_all_sessions')]
    public function getAllSessions(EntityManagerInterface $em): JsonResponse
    {
        $sessions = $em->getRepository(VolunteerSession::class)->findAll();

        $data = array_map(function ($s) {

            $firstNames = [];
            foreach ($s->getVolunteerRegistrations() as $registration) {
                $user = $registration->getUser();
                if ($user) {
                    $firstNames[] = $user->getFirstName();
                }
            }

            return [
                'title' => $s->getTitle(),
                'start' => $s->getStartDatetime()->format(DATE_ATOM),
                'end' => $s->getEndDatetime()->format(DATE_ATOM),
                'id' => $s->getId(),
                'frequency' => $s->getRecurrence()?->getFrequency(),
                'description' => $s->getDescription(),
                'location' => $s->getLocation(),
                'until' => $s->getRecurrence()?->getUntilDate()?->format('Y-m-d'),
                'registeredVolunteers' => count($firstNames),
                'requiredVolunteers' => $s->getRequiredVolunteers(),
                'volunteerFirstNames' => $firstNames,
            ];
        }, $sessions);

        dump($data);

        return $this->json($data);
    }

    #[Route('/schedule/{id}/edit', name: 'app_dashboard_schedule_edit', methods: ['POST'])]
    public function editEvent(VolunteerSession $session, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(VolunteerSessionEditType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les champs date et heure séparés
            $fromDate = $form->get('from_date')->getData();
            $fromTime = $form->get('from_time')->getData();
            $toDate = $form->get('to_date')->getData();
            $toTime = $form->get('to_time')->getData();

            // Fusionne la date et l'heure pour une datetime complète
            if ($fromDate && $fromTime) {
                $session->setStartDatetime(\DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i',
                    $fromDate->format('Y-m-d') . ' ' . $fromTime->format('H:i')
                ));
            }
            if ($toDate && $toTime) {
                $session->setEndDatetime(\DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i',
                    $toDate->format('Y-m-d') . ' ' . $toTime->format('H:i')
                ));
            }

            $session->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'errors' => (string) $form->getErrors(true, false)], 400);
    }

    #[Route('/schedule/{id}/delete', name: 'app_dashboard_schedule_delete', methods: ['DELETE'])]
    public function deleteEvent(VolunteerSession $session, EntityManagerInterface $em): JsonResponse
    {
        if ($session) {
            $em->remove($session);
            $em->flush();
            return new JsonResponse(['success' => true]);
        }
        return new JsonResponse(['success' => false], 404);
    }
}
