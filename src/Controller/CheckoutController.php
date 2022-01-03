<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Manager\CartManager;
use App\Form\CheckoutType;
use App\Entity\OrderAddress;
use App\Entity\OrderUser;
use App\Entity\Order;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function index(Request $request, CartManager $cartManager, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $orderUser = new OrderUser();
        $orderAddress = new OrderAddress();

        $cart = $cartManager->getCurrentCart();

        if( $user ) {
            $orderUser->setUser($user);
        } 

        $cart->setUser($orderUser);
        $cart->setAddress($orderAddress);

        $form = $this->createForm(CheckoutType::class, $cart);
        $form->handleRequest($request);

        if( !$form->isSubmitted() && $user ) {
            $formUser = $form->get('user');
            $formUser->get('firstname')->setData($user->getFirstname());
            $formUser->get('lastname')->setData($user->getLastname());
            $formUser->get('email')->setData($user->getEmail());

            $formAddress = $form->get('address');
            $userAddress = $user->getAddress();

            if( $userAddress ) {
                $formAddress->get('city')->setData($userAddress->getCity());
                $formAddress->get('street')->setData($userAddress->getStreet());
                $formAddress->get('number')->setData($userAddress->getNumber());
                $formAddress->get('postalCode')->setData($userAddress->getPostalCode());
                $formAddress->get('phone')->setData($userAddress->getPhone());
                $formAddress->get('country')->setData($userAddress->getCountry());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setStatus(Order::STATUS_ORDER);
            $entityManager->persist($orderUser);     
            $entityManager->persist($orderAddress);     
            
            $entityManager->persist($cart);        
            $entityManager->flush($cart);

            return $this->redirectToRoute('index');
        }

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }
}
