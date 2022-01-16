<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\AttributeValueRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function index(Category $category, ProductRepository $productRepository, AttributeValueRepository $attributeValueRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findBy(['category' => $category]);
        $suppliers = [];
        $attributes = [];

        foreach( $products as $product ) {
            if( !in_array($product->getSupplier(), $suppliers) ) {
                array_push($suppliers, $product->getSupplier());
            } 

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
            'title' => $category->getName(),
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
