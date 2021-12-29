<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\ProductSupplier;
use App\Form\ProductSupplierType;
use App\Repository\ProductSupplierRepository;
use App\Service\SupplierFileUploader;

class SupplierAdminController extends AbstractController
{
    #[Route('/admin/suppliers', name: 'admin-suppliers')]
    public function suppliers(ProductSupplierRepository $productSupplierRepository): Response
    {
        return $this->render('admin/supplier/list.html.twig', [
            'suppliers' => $productSupplierRepository->findAll()
        ]);
    }

    #[Route('/admin/suppliers/new', name: 'admin-new-supplier')]
    public function newSupplier(Request $request, EntityManagerInterface $entityManager, SupplierFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $supplier = new ProductSupplier();
        $form = $this->createForm(ProductSupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                $imageFileName = $fileUploader->upload($image);
                $supplier->setImage($imageFileName);
            }

            $entityManager->persist($supplier);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-suppliers');
        }

        return $this->render('admin/supplier/form.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Add supplier'),
            'buttonText' => $translator->trans('Add supplier'),
            'featuredImage' => ''
        ]);
    }

    #[Route('/admin/suppliers/edit/{id}', name: 'admin-edit-supplier')]
    public function editSupplier(ProductSupplier $supplier, Request $request, EntityManagerInterface $entityManager, SupplierFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $prevFeaturedImage = $supplier->getImage();
        $form = $this->createForm(ProductSupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                if( $prevFeaturedImage ) {
                    $fileUploader->remove($prevFeaturedImage);
                }
                $imageFileName = $fileUploader->upload($image);
                $supplier->setImage($imageFileName);
            }

            $entityManager->persist($supplier);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin-suppliers');
        }

        return $this->render('admin/supplier/form.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Edit supplier'),
            'buttonText' => $translator->trans('Edit supplier'),
            'featuredImage' => $prevFeaturedImage
        ]);
    }
}
