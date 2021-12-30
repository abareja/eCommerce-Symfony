<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Repository\ProductRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function index(Category $category, CategoryRepository $CategoryRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'categories' => $CategoryRepository->findAll(),
            'products' => $products
        ]);
    }
}
