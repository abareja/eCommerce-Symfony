<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\ProductSupplier;
use App\Form\ProductSupplierType;

class SupplierAdminController extends AbstractController
{
    #[Route('/admin/suppliers', name: 'admin-suppliers')]
    public function suppliers(): Response
    {
        return $this->render('admin/supplier/list.html.twig');
    }

    #[Route('/admin/suppliers/new', name: 'admin-new-supplier')]
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

        return $this->render('admin/supplier/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
