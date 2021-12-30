<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use App\Service\SupplierFileUploader;

class SupplierAdminController extends AbstractController
{
    #[Route('/admin/suppliers', name: 'admin-suppliers')]
    public function suppliers(SupplierRepository $SupplierRepository): Response
    {
        return $this->render('admin/supplier/list.html.twig', [
            'suppliers' => $SupplierRepository->findAll()
        ]);
    }

    #[Route('/admin/suppliers/new', name: 'admin-new-supplier')]
    public function newSupplier(Request $request, EntityManagerInterface $entityManager, SupplierFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if( $image ) {
                $imageFileName = $fileUploader->upload($image);
                $supplier->setImage($imageFileName);
            }

            $entityManager->persist($supplier);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Supplier added"));

            return $this->redirectToRoute('admin-suppliers');
        }

        return $this->render('admin/supplier/form.html.twig', [
            'form' => $form->createView(),
            'supplier' => null,
            'title' => $translator->trans('Add supplier'),
            'buttonText' => $translator->trans('Add supplier'),
        ]);
    }

    #[Route('/admin/suppliers/edit/{id}', name: 'admin-edit-supplier')]
    public function editSupplier(Supplier $supplier, Request $request, EntityManagerInterface $entityManager, SupplierFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        $prevFeaturedImage = $supplier->getImage();
        $form = $this->createForm(SupplierType::class, $supplier);
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
            
            $this->addFlash('success', $translator->trans("Supplier edited"));

            return $this->redirectToRoute('admin-suppliers');
        }

        return $this->render('admin/supplier/form.html.twig', [
            'form' => $form->createView(),
            'supplier' => $supplier,
            'title' => $translator->trans('Edit supplier'),
            'buttonText' => $translator->trans('Edit supplier'),
        ]);
    }

    #[Route('/admin/suppliers/delete/{id}', name: 'admin-delete-supplier')]
    public function deleteSupplier(Supplier $supplier, EntityManagerInterface $entityManager, SupplierFileUploader $fileUploader, TranslatorInterface $translator): Response
    {
        try {
            $image = $supplier->getImage();
            
            $entityManager->remove($supplier);
            $entityManager->flush();

            if( $image ) {
                $fileUploader->remove($image);
            }
            
            $this->addFlash('success', $translator->trans("Supplier removed"));

            return $this->redirectToRoute('admin-suppliers');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This supplier has connected products, so it can't be removed"));

            return $this->redirectToRoute('admin-edit-supplier', ['id' => $supplier->getId()]);
        }
    }
}
