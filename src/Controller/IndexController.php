<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function indexAction(CategoryRepository $categoryRepository, ProductRepository $productRepository, TranslatorInterface $translator)
    {
        $newProducts = $productRepository->findBy([], ['dateAdded' => 'DESC'], 10);

        return $this->render('index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'newProducts' => $newProducts,
            'newProductsTitle' => $translator->trans('Newest products')
        ]);
    }
}
