<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use App\Repository\CategoryRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, CategoryRepository $CategoryRepository, TranslatorInterface $translator): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(RegistrationType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $form->get('password')->getData())
                );
    
                $entityManager->persist($user);
                $entityManager->flush();    
    
                $this->addFlash('success', $translator->trans("Registered successfully!"));
    
                return $this->redirectToRoute('index');
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("User with this e-mail address already exists!"));
    
            return $this->redirectToRoute('register');
        }
        
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $CategoryRepository->findAll()
        ]);
    }
}
