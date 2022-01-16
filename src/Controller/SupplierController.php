<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Product;
use App\Entity\Supplier;
use App\Repository\ProductRepository;
use App\Repository\ProductAttributeRepository;

class SupplierController extends AbstractController
{
    #[Route('/supplier/{id}', name: 'supplier')]
    public function index(Supplier $supplier, ProductRepository $productRepository, ProductAttributeRepository $productAttributeRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findBy(['supplier' => $supplier]);
        $data = Product::getDataForProducts($products, $productAttributeRepository);
        $attributes = $data['attributes'];

        return $this->render('shop/index.html.twig', [
            'title' => $supplier->getName(),
            'products' => $products,
            'productsJSON' => $serializer->serialize($products, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]),
            'suppliers' => [],
            'attributes' => $attributes
        ]);
    }
}
