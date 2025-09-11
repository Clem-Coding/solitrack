<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Visitor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\VisitorType;
use App\Repository\DonationRepository;
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
        VisitorRepository $visitorRepository
    ): Response {


        $visitor = new Visitor;
        $form = $this->createForm(VisitorType::class, $visitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $visitor->setUser($user);
            $entityManager->persist($visitor);
            $entityManager->flush();

            $this->addFlash('success', 'Le nombre de visiteurs a été enregistré avec succès !');
            return $this->redirectToRoute('app_dashboard_index');
        }


        $recordsData = [];
        $recordsData['entry'] = $donationRepository->getRecordWeightDay();
        $recordsData['sales'] = $salesItemRepository->getRecordWeightDay();
        $recordsData['visitors'] = $visitorRepository->getRecordWeightDay();
        $recordsData['sales_revenue'] = $saleRepository->getRecordWeightDay();


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


        return $this->render('dashboard/index.html.twig', [

            'form' => $form,
            'points' => $points,
            'recordsData' => $recordsData
        ]);
    }
}
