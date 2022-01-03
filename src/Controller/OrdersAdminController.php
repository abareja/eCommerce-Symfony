<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Order;
use App\Repository\OrderRepository;

class OrdersAdminController extends AbstractController
{
    #[Route('/admin/orders', name: 'admin-orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();

        return $this->render('admin/orders/list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/admin/orders/{id}', name: 'admin-order')]
    public function order(Order $order): Response
    {
        return $this->render('admin/orders/order.html.twig', [
            'order' => $order
        ]);
    }
}
