<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Product;
use App\Repository\CategoryRepository;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product')]
    public function index(Product $product, CategoryRepository $CategoryRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'product' => $product,
            'categories' => $CategoryRepository->findAll()
        ]);
    }
}
