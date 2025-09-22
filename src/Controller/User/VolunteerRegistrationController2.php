<?php
// TESTS 

// namespace App\Controller\User;

// use App\Entity\User;
// use App\Entity\VolunteerRegistration;
// use App\Entity\VolunteerSession;
// use App\Form\VolunteerRegistrationType;
// use App\Repository\VolunteerRegistrationRepository;
// use App\Repository\VolunteerSessionRepository;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Security\Http\Attribute\CurrentUser;

// final class VolunteerRegistrationController2 extends AbstractController
// {
//     #[Route('/volunteer/registration', name: 'app_volunteer_registration')]
//     public function register(
//         Request $request,
//         EntityManagerInterface $em,
//         #[CurrentUser] User $user,
//         VolunteerSessionRepository $volunteerSessionRepository,
//         VolunteerRegistrationRepository $volunteerRegistrationRepository
//     ): Response {

//         $threeLastSession = $volunteerSessionRepository->findLastThreeSessions();

//         $sessionsWithCount = [];
//         foreach ($threeLastSession as $session) {
//             $count = count($volunteerRegistrationRepository->findBy([
//                 'session' => $session,
//                 'status' => 'registered'
//             ]));
//             $sessionsWithCount[] = [
//                 'id' => $session->getId(),
//                 'title' => $session->getTitle(),
//                 'startDatetime' => $session->getStartDatetime(),
//                 'endDatetime' => $session->getEndDatetime(),
//                 'count' => $count
//             ];
//         }

//         $registrationsByUser = $volunteerRegistrationRepository->findBy(['user' => $user]);

//         $registration = new VolunteerRegistration();
//         $form = $this->createForm(VolunteerRegistrationType::class, $registration);
//         $form->handleRequest($request);

//         if ($request->isMethod('POST') && $request->request->get('session_id')) {

//             $sessionId = $request->request->get('session_id');
//             $session = $em->getRepository(VolunteerSession::class)->find($sessionId);

//             // Vérifie si le bénévole est déjà inscrit à la session
//             $existingRegistration = $volunteerRegistrationRepository->findOneBy([
//                 'user' => $user,
//                 'session' => $session,
//                 'status' => 'registered'
//             ]);

//             if ($existingRegistration) {
//                 $this->addFlash('warning', 'Vous êtes déjà inscrit à ce créneau.');
//                 return $this->redirectToRoute('app_volunteer_registration');
//             }

//             $registration->setSession($session);
//             $registration->setStatus("registered");
//             $registration->setUser($user);
//             $registration->setRegisteredAt(new \DateTimeImmutable());
//             $registration->setUpdatedAt(new \DateTimeImmutable());

//             $em->persist($registration);
//             $em->flush();

//             return $this->redirectToRoute('app_volunteer_registration');
//         }
//         return $this->render('volunteer_registration/index.html.twig', [
//             'form' => $form->createView(),
//             'sessions_with_count' => $sessionsWithCount,
//             'registrations_by_user' => $registrationsByUser
//         ]);
//     }

//     #[Route('/volunteer/registration/cancel/{id}', name: 'app_volunteer_registration_cancel')]
//     public function cancelRegistration(int $id, EntityManagerInterface $em, #[CurrentUser] User $user): Response
//     {
//         $registration = $em->getRepository(VolunteerRegistration::class)->find($id);

//         if ($registration && $registration->getUser() === $user) {
//             $registration->setStatus('canceled');
//             $registration->setUpdatedAt(new \DateTimeImmutable());
//             $em->flush();
//         }

//         return $this->redirectToRoute('app_volunteer_registration');
//     }
// }
