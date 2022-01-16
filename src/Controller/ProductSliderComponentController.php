<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Category;
use App\Entity\Product;

use App\Repository\ProductRepository;

class ProductSliderComponentController extends AbstractController
{
    public function new($limit, ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        $newProducts = $productRepository->findBy([], ['dateAdded' => 'DESC'], $limit);

        return $this->render('components/product-slider.html.twig', [
            'products' => $newProducts,
            'title' => $translator->trans('Newest products')
        ]);
    }

    public function bestsellers($limit, ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        $newProducts = $productRepository->bestsellers($limit);

        return $this->render('components/product-slider.html.twig', [
            'products' => $newProducts,
            'title' => $translator->trans('Bestsellers')
        ]);
    }

    public function category(Category $category, Product $product = null, $limit, ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        $products = $productRepository->findBy(['category' => $category], ['dateAdded' => 'DESC'], $limit);

        if( $product ) {
            foreach( $products as $key => $tmp ) {
                if( $tmp->getId() == $product->getId() ) {
                    unset($products[$key]);
                }
            }
        }

        return $this->render('components/product-slider.html.twig', [
            'products' => $products,
            'title' => $translator->trans('Other products from this category')
        ]);
    }
}
