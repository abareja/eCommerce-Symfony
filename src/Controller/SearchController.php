<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Product;
use App\Repository\ProductAttributeRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function index(Request $request, ProductRepository $productRepository = null, ProductAttributeRepository $productAttributeRepository = null, TranslatorInterface $translator, SerializerInterface $serializer): Response
    {
        $query = $request->query->get('search');
        
        $products = $productRepository->search($query);
        $data = Product::getDataForProducts($products, $productAttributeRepository);
        $suppliers = $data['suppliers'];
        $attributes = $data['attributes'];

        return $this->render('shop/index.html.twig', [
            'title' => $translator->trans('Search results'),
            'products' => $products,
            'productsJSON' => $serializer->serialize($products, 'json', ['groups' => ['product']]),
            'suppliers' => $suppliers,
            'attributes' => $attributes
        ]);
    }
}
