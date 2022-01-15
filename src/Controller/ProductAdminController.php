<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use App\Entity\Product;
use App\Entity\Image;
use App\Service\ProductFileUploader;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Entity\AttributeValue;
use App\Entity\ProductAttribute;
use App\Entity\Attribute;

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
    public function newProduct(Request $request, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        try {
            $product = new Product();
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $images = $form->get('images')->getData();
                $featuredImage = $form->get('featuredImage')->getData();
                $productAttributes = $form->get('productAttributes');

                $product->setDateAdded(new \Datetime());
                $entityManager->persist($product);
                $entityManager->flush();

                if ( count($images) > 0 ) {
                    foreach( $images as $image ) {
                        $imageFileName = $fileUploader->upload($image);
                        $imageObj = new Image();
                        $imageObj->setFilename($imageFileName);
                        $product->addImage($imageObj);
                    }
                    $entityManager->flush();
                }
    
                if( $featuredImage ) {
                    $featuredImageFileName = $fileUploader->upload($featuredImage);
                    $product->setFeaturedImage($featuredImageFileName);
                    $entityManager->flush();
                }

                if( count($productAttributes) > 0 ) {
                    foreach( $productAttributes as $productAttribute ) {
                        $attribute = $productAttribute->get('attribute')->getData();
                        $attributeValue = $productAttribute->get('attributeValue')->getData();
                        $attributeValue->setAttribute($attribute);

                        $productAttributeObj = new ProductAttribute();
                        $productAttributeObj->setAttribute($attribute);
                        $productAttributeObj->setAttributeValue($attributeValue);
                        $product->addProductAttribute($productAttributeObj);
                    }
                    $entityManager->flush();
                }
    
                $this->addFlash('success', $translator->trans("Product added!"));
    
                return $this->redirectToRoute('admin-products');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Product already exists!"));
    
            return $this->redirectToRoute('admin-new-product');
        }

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'product' => null,
            'title' => $translator->trans('Add product'),
            'buttonText' => $translator->trans('Add product'),
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin-edit-product')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        try {
            $prevFeaturedImage = $product->getFeaturedImage();
            $prevImages = $product->getImages();
            $prevProductAttributes = $product->getProductAttributes();
            $form = $this->createForm(ProductType::class, $product);
            $form->get('productAttributes')->setData($prevProductAttributes);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $images = $form->get('images')->getData();
                $featuredImage = $form->get('featuredImage')->getData();
                $productAttributes = $form->get('productAttributes');
    
                $entityManager->persist($product);
                $entityManager->flush();

                if ( count($images) > 0 ) {
                    if( count($prevImages) > 0) {
                        foreach( $prevImages as $prevImage ) {
                            $fileUploader->remove($prevImage);
                            $entityManager->remove($prevImage);
                            $entityManager->flush();
                        }
                    }
    
                    foreach( $images as $image ) {
                        $imageFileName = $fileUploader->upload($image);
                        $imageObj = new Image();
                        $imageObj->setFilename($imageFileName);
                        $product->addImage($imageObj);
                    }
                    $entityManager->flush();
                }
    
                if( $featuredImage ) {
                    if( $prevFeaturedImage ) {
                        $fileUploader->remove($prevFeaturedImage);
                    }
                    $featuredImageFileName = $fileUploader->upload($featuredImage);
                    $product->setFeaturedImage($featuredImageFileName);
                    $entityManager->flush();
                }

                if( count($prevProductAttributes) > 0) {
                    foreach( $prevProductAttributes as $prevProductAttribute ) {
                        $entityManager->remove($prevProductAttribute);
                        $entityManager->flush();
                    }
                }

                if( count($productAttributes) > 0 ) { 
                    foreach( $productAttributes as $productAttribute ) {
                        $attribute = $productAttribute->get('attribute')->getData();
                        $attributeValue = $productAttribute->get('attributeValue')->getData();
                        
                        $attributeValue->setAttribute($attribute);

                        $productAttributeObj = new ProductAttribute();
                        $productAttributeObj->setAttribute($attribute);
                        $productAttributeObj->setAttributeValue($attributeValue);
                        $product->addProductAttribute($productAttributeObj);
                    }
                    $entityManager->flush();
                }
    
                $this->addFlash('success', $translator->trans("Product edited!"));
    
                return $this->redirectToRoute('admin-products');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Product already exists!"));
    
            return $this->redirectToRoute('admin-edit-product', ['id' => $product->getId()]);
        }
        
        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'title' => $translator->trans('Edit product'),
            'buttonText' => $translator->trans('Edit product'),
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'admin-delete-product')]
    public function deleteSupplier(Product $product, EntityManagerInterface $entityManager, ProductFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        try {
            $featuredImage = $product->getFeaturedImage();
            $images = $product->getImages();
            
            $entityManager->remove($product);
            $entityManager->flush();

            if( $featuredImage ) {
                $fileUploader->remove($featuredImage);
            }

            if( count($images) > 0 ) {
                foreach( $images as $image ) {
                    $fileUploader->remove($image);
                }
            }
            
            $this->addFlash('success', $translator->trans("Product removed!"));

            return $this->redirectToRoute('admin-products');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This product can't be removed!"));

            return $this->redirectToRoute('admin-edit-product', ['id' => $product->getId()]);
        }
    }
}
