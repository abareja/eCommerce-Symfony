<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Manager\CartManager;
use App\Form\CheckoutType;
use App\Entity\Address;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function index(Request $request, CartManager $cartManager, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $address = new Address();

        if( $user ) {
            $address = $user->getAddress();
        }

        $cart = $cartManager->getCurrentCart();
        $cart->setAddress($address);

        $form = $this->createForm(CheckoutType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setStatus('order');
            
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
