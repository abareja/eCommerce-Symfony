<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProductRepository;

class SearchController extends AbstractController
{
    #[Route('/search/{query}', name: 'search')]
    public function index(string $query, ProductRepository $productRepository): Response
    {
        $products = $productRepository->search($query);
        $suppliers = [];

        foreach( $products as $product ) {
            if( !in_array($product->getSupplier(), $suppliers) ) {
                array_push($suppliers, $product->getSupplier());
            } 
        }

        return $this->render('shop/index.html.twig', [
            'title' => 'Search',
            'products' => $products,
            'suppliers' => $suppliers
        ]);
    }
}
