<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Repository\SupplierRepository;

class SupplierSliderComponentController extends AbstractController
{
    public function index(SupplierRepository $supplierRepository = null, TranslatorInterface $translator): Response
    {
        $suppliers = $supplierRepository->findAll();

        return $this->render('components/supplier-slider.html.twig', [
            'suppliers' => $suppliers,
            'title' => $translator->trans('Suppliers')
        ]);
    }
}
