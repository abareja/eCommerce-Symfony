<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\ProductAttributeRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function index(Category $category, ProductRepository $productRepository = null, ProductAttributeRepository $productAttributeRepository = null, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findBy(['category' => $category]);
        $data = Product::getDataForProducts($products, $productAttributeRepository);
        $suppliers = $data['suppliers'];
        $attributes = $data['attributes'];

        return $this->render('shop/index.html.twig', [
            'title' => $category->getName(),
            'products' => $products,
            'productsJSON' => $serializer->serialize($products, 'json', ['groups' => ['product']]),
            'suppliers' => $suppliers,
            'attributes' => $attributes
        ]);
    }
}
