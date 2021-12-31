<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function index(Category $category, CategoryRepository $CategoryRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $category]);
        $suppliers = [];

        foreach( $products as $product ) {
            if( !in_array($product->getSupplier(), $suppliers) ) {
                array_push($suppliers, $product->getSupplier());
            } 
        }

        return $this->render('shop/index.html.twig', [
            'title' => $category->getName(),
            'categories' => $CategoryRepository->findAll(),
            'products' => $products,
            'suppliers' => $suppliers
        ]);
    }
}
