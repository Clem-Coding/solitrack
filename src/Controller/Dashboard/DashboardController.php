<?php

namespace App\Controller\Dashboard;

use App\Entity\OutgoingWeighing;
use App\Entity\User;
use App\Entity\Visitor;
use App\Form\OutgoingWeighingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\VisitorType;
use App\Repository\DonationRepository;
use App\Repository\OutgoingWeighingRepository;
use App\Repository\SaleRepository;
use App\Repository\SalesItemRepository;
use App\Repository\VisitorRepository;
use App\Service\GeocoderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


#[Route('/tableau-de-bord')]
final class DashboardController extends AbstractController
{

    #[Route('', name: 'app_dashboard_index', methods: ['GET', 'POST'])]
    public function index(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        SaleRepository $saleRepository,
        GeocoderService $geocoderService,
        DonationRepository $donationRepository,
        SalesItemRepository $salesItemRepository,
        VisitorRepository $visitorRepository,
    ): Response {


        // VISITOR FORM
        $visitor = new Visitor;
        $form = $this->createForm(VisitorType::class, $visitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $visitor->setUser($user);
            $entityManager->persist($visitor);
            $entityManager->flush();

            $this->addFlash('success_visitors', 'Le nombre de visiteurs a été enregistré avec succès !');
            return $this->redirectToRoute('app_dashboard_index');
        }


        // RECORDS DATA
        $recordsData = [];
        $recordsData['entry'] = $donationRepository->getRecordWeightDay();
        $recordsData['sales'] = $salesItemRepository->getRecordWeightDay();
        $recordsData['visitors'] = $visitorRepository->getRecordWeightDay();
        $recordsData['sales_revenue'] = $saleRepository->getRecordWeightDay();


        // GEOCODE VISITORS DATA
        $cityVisitorsData = $saleRepository->countVisitorsByCity();

        $points = [];
        foreach ($cityVisitorsData as $entry) {
            $coords = $geocoderService->geocode($entry['city'], $entry['zipcode']);

            if ($coords) {
                $points[] = [
                    'lat' => $coords['lat'],
                    'lon' => $coords['lon'],
                    'zipcode' => $coords['postcode'],
                    'city' => $coords['city'],
                    'visitorCount' => $entry['visitorCount']
                ];
            }
        }

        // OUTGOING WEIGHING FORM

        $outgoingWeighing = new OutgoingWeighing();
        $outgoingWeighingForm = $this->createForm(OutgoingWeighingType::class, $outgoingWeighing);
        $outgoingWeighingForm->handleRequest($request);

        if ($outgoingWeighingForm->isSubmitted() && $outgoingWeighingForm->isValid()) {

            $outgoingWeighing->setUser($user);
            $outgoingWeighing->setType($outgoingWeighingForm->get('type')->getData());
            $outgoingWeighing->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($outgoingWeighing);
            $entityManager->flush();

            $this->addFlash('success_outgoing', 'La pesée sortante a été enregistrée avec succès !');
            return $this->redirectToRoute('app_dashboard_index');
        }

        return $this->render('dashboard/index.html.twig', [

            'form' => $form,
            'points' => $points,
            'records_data' => $recordsData,
            'outgoing_weighing_form' => $outgoingWeighingForm
        ]);
    }
}
