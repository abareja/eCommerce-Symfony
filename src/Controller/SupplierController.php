<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Supplier;
use App\Repository\ProductRepository;
use App\Repository\AttributeValueRepository;

class SupplierController extends AbstractController
{
    #[Route('/supplier/{id}', name: 'supplier')]
    public function index(Supplier $supplier, ProductRepository $productRepository, AttributeValueRepository $attributeValueRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findBy(['supplier' => $supplier]);
        $suppliers = [];
        $attributes = [];

        foreach( $products as $product ) {
            $productAttributes = $product->getProductAttributes();

            foreach( $productAttributes as $productAttribute ) {
                $attribute = $productAttribute->getAttribute();
                $attributeValues = $attributeValueRepository->findBy(['attribute' => $attribute]);

                $attributeArr = ['attribute' => $attribute, 'attributeValues' => $attributeValues];

                if( !in_array($attribute, array_column($attributes, 'attribute')) ) {
                    array_push($attributes, $attributeArr);
                }
            }
        }

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
