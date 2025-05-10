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

    public function add(int $id, int $quantity = 1): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }
        $this->session->set('cart', $cart);
    }

    public function remove(int $id): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $this->session->set('cart', $cart);
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
    public function clearAll(): void
{
    $this->session->remove('cart');
}

public function updateQuantity(int $id, int $quantity): void
{
    $cart = $this->session->get('cart', []);
    if (isset($cart[$id])) {
        if ($quantity > 0) {
            $cart[$id] = $quantity;
        } else {
            unset($cart[$id]); // If quantity is 0 or less, remove item
        }
        $this->session->set('cart', $cart);
    }
}

}