<?php

namespace App\Form;

use App\Entity\Wishlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishlistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameproduct')
            ->add('image')
            ->add('productdescription')
            ->add('priceproduct')
            ->add('idproduct')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wishlist::class,
        ]);
    }
}
