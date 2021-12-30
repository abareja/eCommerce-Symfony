<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(CategoryRepository $CategoryRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'categories' => $CategoryRepository->findAll()
        ]);
    }
}
