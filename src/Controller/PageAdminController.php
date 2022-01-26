<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageAdminController extends AbstractController
{
    #[Route('/admin/pages', name: 'admin-pages')]
    public function pages(PageRepository $pageRepository = null): Response
    {
        return $this->render('admin/page/list.html.twig', [
            'pages' => $pageRepository->findAll()
        ]);
    }

    #[Route('/admin/pages/new', name: 'admin-new-page')]
    public function newPage(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($page);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Page added!"));

            return $this->redirectToRoute('admin-pages');
        }
        
        return $this->render('admin/page/form.html.twig', [
            'form' => $form->createView(),
            'page' => null,
            'title' => $translator->trans('Add page'),
            'buttonText' => $translator->trans('Add page'),
        ]);
    }

    #[Route('/admin/pages/edit/{id}', name: 'admin-edit-page')]
    public function editPage(Page $page, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($page);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Page edited!"));

            return $this->redirectToRoute('admin-pages');
        }
        
        return $this->render('admin/page/form.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
            'title' => $translator->trans('Edit page'),
            'buttonText' => $translator->trans('Edit page'),
        ]);
    }

    #[Route('/admin/pages/delete/{id}', name: 'admin-delete-page')]
    public function deletePage(Page $page, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $entityManager->remove($page);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans("Page removed!"));

        return $this->redirectToRoute('admin-pages');
    }
}
