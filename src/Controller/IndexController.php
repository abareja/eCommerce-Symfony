<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function indexAction(CategoryRepository $CategoryRepository)
    {
        return $this->render('index.html.twig', [
            'categories' => $CategoryRepository->findAll()
        ]);
    }
}
