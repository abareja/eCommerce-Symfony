<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryFileUploader;

class CategoryAdminController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin-categories')]
    public function categories(CategoryRepository $CategoryRepository): Response
    {
        return $this->render('admin/category/list.html.twig', [
            'categories' => $CategoryRepository->findAll()
        ]);
    }

    #[Route('/admin/categories/new', name: 'admin-new-category')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                $imageFileName = $fileUploader->upload($image);
                $category->setImage($imageFileName);
            }
            
            $entityManager->persist($category);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Category added"));

            return $this->redirectToRoute('admin-categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'category' => null,
            'title' => $translator->trans('Add category'),
            'buttonText' => $translator->trans('Add category'),
        ]);
    }

    #[Route('/admin/categories/edit/{id}', name: 'admin-edit-category')]
    public function editCategory(Category $category, Request $request, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $prevFeaturedImage = $category->getImage();
        $form = $this->createForm(CategoryType::class, $category);
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
            
            $this->addFlash('success', $translator->trans("Category edited"));

            return $this->redirectToRoute('admin-categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'title' => $translator->trans('Edit category'),
            'buttonText' => $translator->trans('Edit category'),
        ]);
    }

    #[Route('/admin/categories/delete/{id}', name: 'admin-delete-category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager, CategoryFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        try {
            $image = $category->getImage();
            
            $entityManager->remove($category);
            $entityManager->flush();

            if( $image ) {
                $fileUploader->remove($image);
            }

            $this->addFlash('success', $translator->trans("Category removed"));

            return $this->redirectToRoute('admin-categories');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This category has connected products, so it can't be removed"));

            return $this->redirectToRoute('admin-edit-category', ['id' => $category->getId()]);
        }
    }
}
