<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ProductAttributeRepository;

class ShopController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(ProductRepository $productRepository, ProductAttributeRepository $productAttributeRepository, SerializerInterface $serializer, TranslatorInterface $translator): Response
    {
        $products = $productRepository->findBy([], ['dateAdded' => 'DESC'], 10);
        $data = Product::getDataForProducts($products, $productAttributeRepository);
        $suppliers = $data['suppliers'];
        $attributes = $data['attributes'];

        return $this->render('shop/index.html.twig', [
            'title' => $translator->trans('Newest products'),
            'products' => $products,
            'productsJSON' => $serializer->serialize($products, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]),
            'suppliers' => $suppliers,
            'attributes' => $attributes
        ]);
    }

    #[Route('/sale', name: 'sale')]
    public function sale(ProductRepository $productRepository, ProductAttributeRepository $productAttributeRepository, SerializerInterface $serializer, TranslatorInterface $translator): Response
    {
        $products = $productRepository->sale();
        $data = Product::getDataForProducts($products, $productAttributeRepository);
        $suppliers = $data['suppliers'];
        $attributes = $data['attributes'];

        return $this->render('shop/index.html.twig', [
            'title' => $translator->trans('Sale'),
            'products' => $products,
            'productsJSON' => $serializer->serialize($products, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]),
            'suppliers' => $suppliers,
            'attributes' => $attributes
        ]);
    }
}
