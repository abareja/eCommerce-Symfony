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

use App\Entity\Shipping;
use App\Form\ShippingType;

class ShippingAdminController extends AbstractController
{
    #[Route('/admin/shippings/new', name: 'admin-new-shipping')]
    public function newShipping(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $shipping = new Shipping();
            $form = $this->createForm(ShippingType::class, $shipping);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($shipping);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Shipping method added!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Shipping method already exists!"));

            return $this->redirectToRoute('admin-new-shipping');
        }
        
        return $this->render('admin/shipping/form.html.twig', [
            'form' => $form->createView(),
            'shipping' => null,
            'title' => $translator->trans('Add shipping'),
            'buttonText' => $translator->trans('Add shipping'),
        ]);
    }

    #[Route('/admin/shippings/edit/{id}', name: 'admin-edit-shipping')]
    public function editShipping(Shipping $shipping, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $form = $this->createForm(ShippingType::class, $shipping);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($shipping);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Shipping method edited!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Shipping method already exists!"));

            return $this->redirectToRoute('admin-edit-shipping', ['id' => $shipping->getId()]);
        }
        
        return $this->render('admin/shipping/form.html.twig', [
            'form' => $form->createView(),
            'shipping' => $shipping,
            'title' => $translator->trans('Edit shipping'),
            'buttonText' => $translator->trans('Edit shipping'),
        ]);
    }

    #[Route('/admin/shippings/delete/{id}', name: 'admin-delete-shipping')]
    public function deleteShipping(Shipping $shipping, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $entityManager->remove($shipping);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Shipping method removed!"));

            return $this->redirectToRoute('admin-settings');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This shipping has connected orders, so it can't be removed!"));

            return $this->redirectToRoute('admin-edit-shipping', ['id' => $shipping->getId()]);
        }
    }
}
