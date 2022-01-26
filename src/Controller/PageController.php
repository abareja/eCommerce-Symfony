<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Component\BrowserKit\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageController extends AbstractController
{   
    #[Route('/page/title/{title}', name: 'page-by-title')]
    public function pageByTitle(Page $page): Response
    {
        return $this->redirectToRoute('page', ['id' => $page->getId()]);
    }

    #[Route('/page/{id}', name: 'page')]
    public function page(Page $page): Response
    {
        return $this->render('page/index.html.twig', [
            'page' => $page
        ]);
    }

    public function getPageGroup($group, PageRepository $pageRepository = null, TranslatorInterface $translator): Response
    {
        $pages = $pageRepository->findBy(['pageGroup' => $group]);
        $title = "Pages";

        if( count($pages) > 0 ) {
            $title = $pages[0]->getPageGroupLabel();
        }

        return $this->render('page/group.html.twig', [
            'title' => $translator->trans($title),
            'pages' => $pages
        ]);
    }
}
