<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\LivresRepository;

class CartService
{
    private $session;
    private $livresRepository;

    public function __construct(RequestStack $requestStack, LivresRepository $livresRepository)
    {
        $this->session = $requestStack->getSession();
        $this->livresRepository = $livresRepository;
    }

    public function add(int $id, int $quantity = 1): bool
    {
        $livre = $this->livresRepository->find($id);
        if (!$livre) {
            return false;
        }

        $stock = $livre->getQuantiteDisponible();
        $cart = $this->session->get('cart', []);
        $current = $cart[$id] ?? 0;

        if ($current + $quantity > $stock) {
            return false; // Not enough stock
        }

        $cart[$id] = $current + $quantity;
        $this->session->set('cart', $cart);
        return true;
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        $livre = $this->livresRepository->find($id);
        if (!$livre) {
            return false;
        }

        $stock = $livre->getQuantiteDisponible();
        $cart = $this->session->get('cart', []);

        if ($quantity > $stock) {
            return false;
        }

        if ($quantity > 0) {
            $cart[$id] = $quantity;
        } else {
            unset($cart[$id]); // Remove if 0
        }

        $this->session->set('cart', $cart);
        return true;
    }

    public function remove(int $id): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $this->session->set('cart', $cart);
    }

    public function clearAll(): void
    {
        $this->session->remove('cart');
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function getFullCart(): array
    {
        $cart = $this->getCart();
        $fullCart = [];

        foreach ($cart as $id => $quantity) {
            $livre = $this->livresRepository->find($id);
            if ($livre) {
                $fullCart[] = [
                    'livre' => $livre,
                    'quantity' => $quantity,
                ];
            }
        }

        return $fullCart;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['livre']->getPrix() * $item['quantity'];
        }
        return $total;
    }
}
