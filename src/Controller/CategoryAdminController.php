<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\ProductCategory;
use App\Service\CategoryFileUploader;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;

class CategoryAdminController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin-categories')]
    public function categories(ProductCategoryRepository $productCategoryRepository): Response
    {
        return $this->render('admin/category/list.html.twig', [
            'categories' => $productCategoryRepository->findAll()
        ]);
    }

    #[Route('/admin/categories/new', name: 'admin-new-category')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $category = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                $imageFileName = $fileUploader->upload($image);
                $category->setImage($imageFileName);
            }
            
            $entityManager->persist($category);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Add category'),
            'buttonText' => $translator->trans('Add category'),
            'featuredImage' => '',
        ]);
    }

    #[Route('/admin/categories/edit/{id}', name: 'admin-edit-category')]
    public function editCategory(ProductCategory $category, Request $request, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $prevFeaturedImage = $category->getImage();
        $form = $this->createForm(ProductCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                if( $prevFeaturedImage ) {
                    $fileUploader->remove($prevFeaturedImage);
                }
                $imageFileName = $fileUploader->upload($image);
                $category->setImage($imageFileName);
            }
            
            $entityManager->persist($category);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Edit category'),
            'buttonText' => $translator->trans('Edit category'),
            'featuredImage' => $prevFeaturedImage,
        ]);
    }
}
