<?php

namespace App\Controller;

use App\Entity\SalesItem;
use App\Service\PriceManagement;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[IsGranted('IS_AUTHENTICATED')]


// ROUTE?
final class CartController extends AbstractController
{

    #[Route('/cart/remove-item', name: 'app_cart_remove-item', methods: ['POST'])]
    public function removeItem(Request $request, SessionInterface $session, PriceManagement $priceManagement)
    {
        try {

            $data = json_decode($request->getContent(), true);
            $id = $data['id'];

            $shoppingCart = $session->get('shopping_cart', []);


            $found = false;
            foreach ($shoppingCart as $key => $item) {
                if ($item['uuid'] === $id) {
                    unset($shoppingCart[$key]);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return $this->json(['status' => 'error', 'message' => 'Article non trouvé dans le panier.']);
            }


            $shoppingCart = array_values($shoppingCart); // Réindexation
            $session->set('shopping_cart', $shoppingCart);
            $total = $priceManagement->getCartTotal();


            return $this->json([
                'status' => 'success',
                'cart' => $shoppingCart,
                'total' => $total
            ]);
        } catch (\Exception $e) {

            return $this->json(['status' => 'error', 'message' => 'Erreur interne du serveur: ' . $e->getMessage()]);
        }
    }







    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session)
    {
        if ($session->has('shopping_cart')) {
            $session->remove('shopping_cart');



            return $this->json([
                'status' => 'success',
                'message' => 'Panier vidé avec succès',

            ]);
        }



        return $this->json(['status' => 'error', 'message' => 'Le panier était déjà vide']);
    }
}
