<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product')]
    public function index(Product $product, Request $request, CartManager $cartManager, TranslatorInterface $translator): Response
    {
        try {
            $form = $this->createForm(AddToCartType::class);

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $item = $form->getData();
                $item->setProduct($product);

                if( $product->hasDiscount() ) {
                    $item->setPrice($product->getDiscountPrice());
                } else {
                    $item->setPrice($product->getPrice());
                }
    
                $cart = $cartManager->getCurrentCart();
                $cart->addItem($item);
    
                $cartManager->save($cart);

                $this->addFlash('success', $translator->trans("Product added to cart!"));
    
                return $this->redirectToRoute('product', ['id' => $product->getId()]);
            }
        } catch(UniqueConstraintViolationException $e) {
            $this->addFlash('error', $translator->trans("Product already in cart!"));
    
            return $this->redirectToRoute('product', ['id' => $product->getId()]);
        }
        
        return $this->render('product/index.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }
}
