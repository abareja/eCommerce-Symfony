<?php

namespace App\Form;

use App\Entity\Attribute;
use App\Form\AttributeValueType;
use App\Entity\ProductAttribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attribute', EntityType::class, [
                'required' => true,
                'label' => false,
                'class' => Attribute::class,
                'attr' => [
                    'class' => 'o-input js-select2'
                ]
            ])
            ->add('attributeValue', AttributeValueType::class, [
                'required' => true,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductAttribute::class,
        ]);
    }
}
