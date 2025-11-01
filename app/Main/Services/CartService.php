<?php

namespace App\Main\Services;

use App\Main\Repositories\CartRepository;


class CartService
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getByUserId()
    {
        return $this->cartRepository->getByUserId();
    }

    public function getCartItemsByUserId()
{
    return $this->cartRepository->getCartItemsByUserId();
}


    public function addToCart($data)
    {
        return $this->cartRepository->addToCart($data);
    }

    public function updateQuantity($cartId, $quantity)
    {
        return $this->cartRepository->updateQuantity($cartId, $quantity);
    }

    public function removeFromCart($cartId)
    {
        return $this->cartRepository->removeFromCart($cartId);
    }

    public function clearCartForUser()
    {
        return $this->cartRepository->clearCartForUser();
    }

    public function viewCartAndCalculateTotal()
    {
        return $this->cartRepository->viewCartAndCalculateTotal();
    }

    public function submitCart()
    {
        return $this->cartRepository->submitCart();
    }
}




