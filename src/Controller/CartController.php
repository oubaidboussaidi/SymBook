<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CartService;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(CartService $cartService, Request $request, int $id): Response
    {
        $quantity = (int)$request->request->get('quantity', 1);

        if (!$cartService->add($id, $quantity)) {
            $this->addFlash('danger', "La quantité demandée dépasse le stock disponible.");
        } else {
            $this->addFlash('success', "Livre ajouté au panier.");
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(CartService $cartService, int $id): Response
    {
        $cartService->remove($id);
        $this->addFlash('info', "Livre supprimé du panier.");
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/update/{id}', name: 'cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(CartService $cartService, Request $request, int $id): Response
    {
        $quantity = (int)$request->request->get('quantity');

        if (!$cartService->updateQuantity($id, $quantity)) {
            $this->addFlash('danger', "Quantité non disponible en stock.");
        } else {
            $this->addFlash('success', "Quantité mise à jour.");
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/clear', name: 'cart_clear_all', methods: ['POST'])]
    public function clearAll(CartService $cartService): Response
    {
        $cartService->clearAll();
        $this->addFlash('info', "Panier vidé.");
        return $this->redirectToRoute('cart_index');
    }
}
