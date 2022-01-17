<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\AttributeValueRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function index(Request $request, ProductRepository $productRepository, TranslatorInterface $translator, AttributeValueRepository $attributeValueRepository, SerializerInterface $serializer): Response
    {
        $query = $request->query->get('search');
        
        $products = $productRepository->search($query);
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
            'title' => $translator->trans('Search results'),
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
