<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\ProductCategory;
use App\Service\CategoryFileUploader;
use App\Form\ProductCategoryType;

class CategoryAdminController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin-categories')]
    public function categories(): Response
    {
        return $this->render('admin/category/list.html.twig');
    }

    #[Route('/admin/categories/new', name: 'admin-new-category')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader): Response
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

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
