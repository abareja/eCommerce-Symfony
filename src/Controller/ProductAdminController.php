<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Product;
use App\Entity\Image;
use App\Service\ProductFileUploader;
use App\Form\ProductType;
use App\Repository\ProductRepository;

class ProductAdminController extends AbstractController
{
    #[Route('/admin/products', name: 'admin-products')]
    public function products(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/list.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/admin/products/new', name: 'admin-new-product')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $featuredImage = $form->get('featuredImage')->getData();

            if ( count($images) > 0 ) {
                foreach( $images as $image ) {
                    $imageFileName = $fileUploader->upload($image);
                    $imageObj = new Image();
                    $imageObj->setFilename($imageFileName);
                    $product->addImage($imageObj);
                }
            }

            if( $featuredImage ) {
                $featuredImageFileName = $fileUploader->upload($featuredImage);
                $product->setFeaturedImage($featuredImageFileName);
            }

            $entityManager->persist($product);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-products');
        }

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add product',
            'buttonText' => 'Add product',
            'featuredImage' => '',
            'images' => []
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin-edit-product')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader): Response
    {
        $prevFeaturedImage = $product->getFeaturedImage();
        $prevImages = $product->getImages();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $featuredImage = $form->get('featuredImage')->getData();

            if ( count($images) > 0 ) {
                foreach( $images as $image ) {
                    $imageFileName = $fileUploader->upload($image);
                    $imageObj = new Image();
                    $imageObj->setFilename($imageFileName);
                    $product->addImage($imageObj);
                }
            }

            if( $featuredImage ) {
                $featuredImageFileName = $fileUploader->upload($featuredImage);
                $product->setFeaturedImage($featuredImageFileName);
            }

            $entityManager->persist($product);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-products');
        }

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit product',
            'buttonText' => 'Edit product',
            'id' => $product->getId(),
            'featuredImage' => $prevFeaturedImage,
            'images' => $prevImages
        ]);
    }
}
