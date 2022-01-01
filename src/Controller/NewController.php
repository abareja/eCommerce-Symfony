<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Repository\ProductRepository;

class NewController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function index(ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        $products = $productRepository->findBy([], ['dateAdded' => 'DESC'], 10);
        $suppliers = [];

        foreach( $products as $product ) {
            if( !in_array($product->getSupplier(), $suppliers) ) {
                array_push($suppliers, $product->getSupplier());
            } 
        }

        return $this->render('shop/index.html.twig', [
            'title' => $translator->trans('Newest products'),
            'products' => $products,
            'suppliers' => $suppliers
        ]);
    }
}
