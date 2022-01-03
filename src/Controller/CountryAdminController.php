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

use App\Entity\Country;
use App\Form\CountryType;

class CountryAdminController extends AbstractController
{
    #[Route('/admin/countries/new', name: 'admin-new-country')]
    public function newCountry(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $country = new Country();
            $form = $this->createForm(CountryType::class, $country);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            
                $entityManager->persist($country);
                $entityManager->flush();
                
                $this->addFlash('success', $translator->trans("Country added!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Country already exists!"));

            return $this->redirectToRoute('admin-new-country');
        }
        
        return $this->render('admin/country/form.html.twig', [
            'form' => $form->createView(),
            'country' => null,
            'title' => $translator->trans('Add country'),
            'buttonText' => $translator->trans('Add country'),
        ]);
    }

    #[Route('/admin/countries/edit/{id}', name: 'admin-edit-country')]
    public function editCountry(Country $country, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $form = $this->createForm(CountryType::class, $country);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($country);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans("Country edited!"));

                return $this->redirectToRoute('admin-settings');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Country already exists!"));

            return $this->redirectToRoute('admin-edit-country', ['id' => $country->getId()]);
        }
        
        return $this->render('admin/country/form.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
            'title' => $translator->trans('Edit country'),
            'buttonText' => $translator->trans('Edit country'),
        ]);
    }

    #[Route('/admin/countries/delete/{id}', name: 'admin-delete-country')]
    public function deleteCountry(Country $country, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        try {
            $entityManager->remove($country);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Country removed!"));

            return $this->redirectToRoute('admin-settings');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("This country has connected users, so it can't be removed!"));

            return $this->redirectToRoute('admin-edit-country', ['id' => $country->getId()]);
        }
    }
}
