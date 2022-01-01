<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Manager\CartManager;
use App\Repository\CategoryRepository;
use App\Repository\OrderItemRepository;

class SiteHeaderController extends AbstractController
{
    public function index(CartManager $cartManager, CategoryRepository $categoryRepository, OrderItemRepository $orderItemRepository): Response
    {
        $cart = $cartManager->getCurrentCart();
        $cartItemsCount = $cartManager->getCurrentCartCount($orderItemRepository);

        return $this->render('header/site-header.html.twig', [
            'cart' => $cart,
            'cartItemsCount' => $cartItemsCount,
            'categories' => $categoryRepository->findAll()
        ]);
    }
}
