<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductCategoryRepository;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function indexAction(ProductCategoryRepository $productCategoryRepository)
    {
        return $this->render('index.html.twig', [
            'categories' => $productCategoryRepository->findAll()
        ]);
    }
}
