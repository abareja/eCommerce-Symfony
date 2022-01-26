<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;
use App\Repository\CountryRepository;
use App\Repository\ShippingRepository;
use App\Repository\PaymentRepository;
use App\Repository\AttributeRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function admin(ProductRepository $productRepository = null, OrderRepository $orderRepository = null): Response
    {
        $totalEarningsByMonth = [];
        $totals = $orderRepository->getTotals();

        $date = [];
        foreach( $totals as $total ) {
            if( !in_array($total['totalDate'], $date) ) array_push($date, $total['totalDate']);
        }

        foreach( $date as $item ) {
            $sum = 0;

            foreach( $totals as $total ) {
                if( $total['totalDate'] == $item ) $sum += $total['total'];
            }

            $totalsArr = ['date' => $item, 'earnings' => $sum];
            array_push($totalEarningsByMonth, $totalsArr);
        }

        $bestsellers = $productRepository->bestsellers(5);
        $lastOrders = $orderRepository->findBy(['status' => [Order::STATUS_ORDER, Order::STATUS_ARCHIVED, Order::STATUS_FINISHED]], ['createdAt' => 'DESC'], 5);

        return $this->render('admin/index.html.twig', [
            'totalEarningsByMonth' => $totalEarningsByMonth,
            'bestsellers' => $bestsellers,
            'lastOrders' => $lastOrders
        ]);
    }

    #[Route('/admin/settings', name: 'admin-settings')]
    public function adminSettings(CountryRepository $countryRepository = null, ShippingRepository $shippingRepository = null, PaymentRepository $paymentRepository = null, AttributeRepository $attributeRepository = null): Response
    {
        $countries = $countryRepository->findAll();
        $shippings = $shippingRepository->findAll();
        $payments = $paymentRepository->findAll();
        $attributes = $attributeRepository->findAll();

        return $this->render('admin/settings.html.twig', [
            'countries' => $countries, 
            'shippings' => $shippings, 
            'payments' => $payments,
            'attributes' => $attributes
        ]);
    }
}
