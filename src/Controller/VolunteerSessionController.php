<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VolunteerRecurrence;
use App\Form\VolunteerSessionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\VolunteerSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


final class VolunteerSessionController extends AbstractController
{
    #[Route('/mon-compte/benevolat', name: 'app_user_benevolat')]
    public function new(Request $request, EntityManagerInterface $em, #[CurrentUser] User $user): Response
    {
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
            }

            $em->persist($session);
            $em->flush();
            return $this->redirectToRoute('app_user_benevolat');
        }

        return $this->render('volunteer_session/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
