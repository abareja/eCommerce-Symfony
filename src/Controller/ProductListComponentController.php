<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Repository\ProductRepository;

class ProductListComponentController extends AbstractController
{
    public function new($limit, ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        $newProducts = $productRepository->findBy([], ['dateAdded' => 'DESC'], $limit);

        return $this->render('components/product-list.html.twig', [
            'products' => $newProducts,
            'title' => $translator->trans('Newest products')
        ]);
    }
}
