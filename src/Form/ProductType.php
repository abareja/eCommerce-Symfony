<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductSupplier;
use App\Entity\ProductCategory;
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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThan;

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
            ->add('quantityInStock', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0)
                ],
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => ProductCategory::class,
            ])
            ->add('supplier', EntityType::class, [
                'required' => true,
                'class' => ProductSupplier::class,
            ])
            ->add('description', TextareaType::class)
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
