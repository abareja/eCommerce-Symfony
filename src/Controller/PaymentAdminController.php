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

use App\Entity\Payment;
use App\Form\PaymentType;

class PaymentAdminController extends AbstractController
{
    #[Route('/admin/payments/new', name: 'admin-new-payment')]
    public function newPayment(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $payment = new Payment();
            $form = $this->createForm(PaymentType::class, $payment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($payment);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Payment method added!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Payment method already exists!"));

            return $this->redirectToRoute('admin-new-payment');
        }
        
        return $this->render('admin/payment/form.html.twig', [
            'form' => $form->createView(),
            'payment' => null,
            'title' => $translator->trans('Add payment'),
            'buttonText' => $translator->trans('Add payment'),
        ]);
    }

    #[Route('/admin/payments/edit/{id}', name: 'admin-edit-payment')]
    public function editPayment(Payment $payment, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $form = $this->createForm(PaymentType::class, $payment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($payment);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Payment method edited!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Payment method exists!"));

            return $this->redirectToRoute('admin-edit-payment', ['id' => $payment->getId()]);
        }
        
        return $this->render('admin/payment/form.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
            'title' => $translator->trans('Edit payment'),
            'buttonText' => $translator->trans('Edit payment'),
        ]);
    }

    #[Route('/admin/payments/delete/{id}', name: 'admin-delete-payment')]
    public function deletePayment(Payment $payment, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $entityManager->remove($payment);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Payment method removed!"));

            return $this->redirectToRoute('admin-settings');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This payment has connected orders, so it can't be removed!"));

            return $this->redirectToRoute('admin-edit-payment', ['id' => $payment->getId()]);
        }
    }
}
