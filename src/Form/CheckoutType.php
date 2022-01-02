<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Shipping;
use App\Form\AddressType;
use App\Form\UserType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class)
            ->add('address', AddressType::class)
            ->add('payment', EntityType::class, [
                'required' => true,
                'class' => Payment::class
            ])
            ->add('shipping', EntityType::class, [
                'required' => true,
                'class' => Shipping::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
