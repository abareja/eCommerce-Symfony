<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Form\OrderType;

class OrdersAdminController extends AbstractController
{
    #[Route('/admin/orders', name: 'admin-orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => [Order::STATUS_ORDER, Order::STATUS_ARCHIVED, Order::STATUS_FINISHED]]);

        return $this->render('admin/orders/list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/admin/orders/new', name: 'admin-orders-new')]
    public function ordersNew(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_ORDER]);

        return $this->render('admin/orders/list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/admin/orders/finished', name: 'admin-orders-finished')]
    public function ordersFinished(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_FINISHED]);

        return $this->render('admin/orders/list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/admin/orders/archive', name: 'admin-orders-archived')]
    public function ordersArchive(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_ARCHIVED]);

        return $this->render('admin/orders/list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/admin/orders/{id}', name: 'admin-order')]
    public function order(Order $order, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($order);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Order updated!"));

            return $this->redirectToRoute('admin-order', ['id' => $order->getId()]);
        }

        return $this->render('admin/orders/order.html.twig', [
            'form' => $form->createView(),
            'order' => $order
        ]);
    }

    #[Route('/admin/orders/delete/{id}', name: 'admin-delete-order')]
    public function deleteCategory(Order $order, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $entityManager->remove($order);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Order removed!"));

            return $this->redirectToRoute('admin-orders');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This order can't be removed!"));

            return $this->redirectToRoute('admin-order', ['id' => $order->getId()]);
        }
    }
}
