<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/settings', name: 'admin-settings')]
    public function adminSettings(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
