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
            $cart = $cartManager->getCurrentCart();
            $form = $this->createForm(AddToCartType::class);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $item = $form->getData();

                if( $item->getQuantity() > $product->getQuantityInStock() || ($cart->getItemByProduct($product) && ($cart->getItemByProduct($product)->getQuantity() + $item->getQuantity()) > $product->getQuantityInStock()) ) {
                    $this->addFlash('error', $translator->trans("available_products", ['quantity' => $product->getQuantityInStock()]));
    
                    return $this->redirectToRoute('product', ['id' => $product->getId()]);
                }
                $item->setProduct($product);

                if( $product->hasDiscount() ) {
                    $item->setPrice($product->getDiscountPrice());
                } else {
                    $item->setPrice($product->getPrice());
                }
    
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
