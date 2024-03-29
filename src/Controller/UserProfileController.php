<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Form\UserType;
use App\Form\AddressType;
use App\Form\UserPasswordType;
use App\Entity\Order;
use App\Entity\Address;
use App\Entity\OrderUser;
use App\Repository\OrderRepository;
use App\Repository\OrderUserRepository;


class UserProfileController extends AbstractController
{
    #[Route('/profile/user', name: 'profile-edit-user')]
    public function user(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Contact data changed!"));

            return $this->redirectToRoute('profile-edit-user');
        }
        
        return $this->render('profile/user/form-user.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Edit contact data'),
            'buttonText' => $translator->trans('Save')
        ]);
    }

    #[Route('/profile/user-address', name: 'profile-edit-user-address')]
    public function userAddress(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $address = $user->getAddress();

        if( !$address ) $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAddress($address);

            $entityManager->persist($user);
            $entityManager->persist($address);
            $entityManager->flush();
            
            $this->addFlash('success', $translator->trans("Address changed!"));

            return $this->redirectToRoute('profile-edit-user-address');
        }
        
        return $this->render('profile/user/form-address.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Edit address'),
            'buttonText' => $translator->trans('Save')
        ]);
    }

    #[Route('/profile/user-password', name: 'profile-edit-user-password')]
    public function userChangePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            $checkPass = $passwordHasher->isPasswordValid($user, $oldPassword);

            if($checkPass === true) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $newPassword)
                ); 

                $entityManager->persist($user);
                $entityManager->flush();
            } else {
                $this->addFlash('error', $translator->trans("Old password is not correct!"));

                return $this->redirectToRoute('profile-edit-user-password');                }

            $this->addFlash('success', $translator->trans("User password changed!"));

            return $this->redirectToRoute('profile-edit-user-password');
        }
        
        return $this->render('profile/user/form-password.html.twig', [
            'form' => $form->createView(),
            'title' => $translator->trans('Change password'),
            'buttonText' => $translator->trans('Save')
        ]);
    }

    #[Route('/profile/user-orders', name: 'profile-user-orders')]
    public function userOrders(OrderRepository $orderRepository = null, OrderUserRepository $orderUserRepository = null): Response
    {
        $user = $this->getUser();
        $orders = [];
        $orderUsers = $orderUserRepository->findBy(['user' => $user]);

        foreach( $orderUsers as $orderUser ) {
            $orders = array_merge($orders, $orderRepository->findBy(['user' => $orderUser]));
        }
        
        return $this->render('profile/user/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/profile/user-orders/{id}', name: 'profile-user-order')]
    public function userOrder(Order $order): Response
    {
        return $this->render('profile/user/order.html.twig', [
            'order' => $order
        ]);
    }
}
