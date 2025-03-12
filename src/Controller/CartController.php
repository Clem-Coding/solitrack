<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Service\PriceManagement;

use App\Controller\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]


// ROUTE?
final class CartController extends AbstractController
{

    #[Route('/cart/remove-item', name: 'app_cart_remove-item', methods: ['POST'])]
    public function removeItem(Request $request, SessionInterface $session): HttpFoundationJsonResponse
    {
        try {
            // Récupère les données envoyées via POST
            $data = json_decode($request->getContent(), true);
            $id = $data['id'];  // Récupère l'ID de l'article à supprimer


            $shoppingCart = $session->get('shopping_cart', []);

            // Cherche l'élément avec l'ID spécifié et le supprime
            $found = false;
            foreach ($shoppingCart as $key => $item) {
                if ($item->id === $id) {
                    unset($shoppingCart[$key]);
                    $found = true;
                    break;
                }
            }


            if (!$found) {
                return $this->json(['status' => 'error', 'message' => 'Article non trouvé dans le panier.']);
            }

            // Met à jour le panier dans la session
            $session->set('shopping_cart', $shoppingCart);

            // Retourne le panier mis à jour
            return $this->json(['status' => 'success', 'cart' => $shoppingCart]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourne un message d'erreur
            return $this->json(['status' => 'error', 'message' => 'Erreur interne du serveur: ' . $e->getMessage()]);
        }
    }






    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session)
    {
        if ($session->has('shopping_cart')) {
            $session->remove('shopping_cart');
            $this->addFlash('success', 'Le panier a été vidé.');
        }

        return $this->redirectToRoute('app_sales');
    }
}
