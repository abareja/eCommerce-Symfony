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

use App\Entity\Attribute;
use App\Form\AttributeType;

class AttributeAdminController extends AbstractController
{
    #[Route('/admin/attributes/new', name: 'admin-new-attribute')]
    public function newAttribute(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $attribute = new Attribute();
            $form = $this->createForm(AttributeType::class, $attribute);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($attribute);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Attribute added!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Attribute already exists!"));

            return $this->redirectToRoute('admin-new-attribute');
        }
        
        return $this->render('admin/attribute/form.html.twig', [
            'form' => $form->createView(),
            'attribute' => null,
            'title' => $translator->trans('Add attribute'),
            'buttonText' => $translator->trans('Add attribute'),
        ]);
    }

    #[Route('/admin/attributes/edit/{id}', name: 'admin-edit-attribute')]
    public function editAttribute(Attribute $attribute, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $form = $this->createForm(AttributeType::class, $attribute);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($attribute);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Attribute edited!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Attribute already exists!"));

            return $this->redirectToRoute('admin-edit-attribute', ['id' => $attribute->getId()]);
        }
        
        return $this->render('admin/attribute/form.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute,
            'title' => $translator->trans('Edit attribute'),
            'buttonText' => $translator->trans('Edit attribute'),
        ]);
    }

    #[Route('/admin/attributes/delete/{id}', name: 'admin-delete-attribute')]
    public function deleteAttribute(Attribute $attribute, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $entityManager->remove($attribute);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Attribute removed!"));

            return $this->redirectToRoute('admin-settings');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This attribute can't be removed!"));

            return $this->redirectToRoute('admin-edit-attribute', ['id' => $attribute->getId()]);
        }
    }
}
