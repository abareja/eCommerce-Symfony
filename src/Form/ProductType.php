<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Supplier;
use App\Entity\Category;
use App\Entity\ProductAttribute;
use App\Form\ProductAttributeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('price', MoneyType::class, [
                'required' => true,
                'currency' => ''
            ])
            ->add('discountPrice', MoneyType::class, [
                'required' => false,
                'currency' => ''
            ])
            ->add('quantityInStock', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(0)
                ],
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
            ])
            ->add('supplier', EntityType::class, [
                'required' => true,
                'class' => Supplier::class,
            ])
            ->add('description', TextareaType::class)
            ->add('featuredImage', FileType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ]
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '100M',
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                ]
                            ]),
                        ],
                    ])
                ],
            ])
            ->add('productAttributes', CollectionType::class, [
                'mapped' => false,
                'entry_type' => ProductAttributeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'prototype_name' =>  '__parent_name__',
                'label' => false,
                'delete_empty' => function (ProductAttribute $productAttribute) {
                    $attributeValue = $productAttribute->getAttributeValue();
                    return empty($productAttribute->getAttribute()) || empty($productAttribute->getAttributeValue()) || empty($attributeValue->getValue());
                },
                'attr' => array(
                    'class' => 'o-collection js-collection',
                ),
                'entry_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'o-collection__group'
                    ],
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
