<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProductCategoryRepository;
use App\Entity\ProductCategory;
use App\Repository\ProductRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function index(ProductCategory $category, ProductCategoryRepository $productCategoryRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'categories' => $productCategoryRepository->findAll(),
            'products' => $products
        ]);
    }
}
