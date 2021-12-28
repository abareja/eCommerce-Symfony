<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\Image;
use App\Entity\ProductSupplier;
use App\Service\ProductFileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use App\Form\ProductCategoryType;
use App\Form\ProductSupplierType;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/products', name: 'admin-products')]
    public function products(): Response
    {
        return $this->render('admin/products.html.twig');
    }

    #[Route('/admin/new-product', name: 'admin-new-product')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            if ( count($images) > 0 ) {
                foreach( $images as $image ) {
                    $imageFileName = $fileUploader->upload($image);
                    $imageObj = new Image();
                    $imageObj->setFilename($imageFileName);
                    $product->addImage($imageObj);
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-products');
        }

        return $this->render('admin/new-product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/categories', name: 'admin-categories')]
    public function categories(): Response
    {
        return $this->render('admin/categories.html.twig');
    }

    #[Route('/admin/new-category', name: 'admin-new-category')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($category);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/new-category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/suppliers', name: 'admin-suppliers')]
    public function suppliers(): Response
    {
        return $this->render('admin/categories.html.twig');
    }

    #[Route('/admin/new-supplier', name: 'admin-new-supplier')]
    public function newSupplier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $supplier = new ProductSupplier();
        $form = $this->createForm(ProductSupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($supplier);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/new-supplier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
