<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CountryRepository;
use App\Repository\ShippingRepository;
use App\Repository\PaymentRepository;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/settings', name: 'admin-settings')]
    public function adminSettings(CountryRepository $countryRepository, ShippingRepository $shippingRepository, PaymentRepository $paymentRepository): Response
    {
        $countries = $countryRepository->findAll();
        $shippings = $shippingRepository->findAll();
        $payments = $paymentRepository->findAll();

        return $this->render('admin/settings.html.twig', [
            'countries' => $countries, 
            'shippings' => $shippings, 
            'payments' => $payments
        ]);
    }
}
