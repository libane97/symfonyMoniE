<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('description')
            ->add('price')
           // ->add('archived')
           ->add('archived', ChoiceType::class, [
            'choices'  => [
                'archived' => 1,
                'Non archived' => 0,
              ],
             ])
           // ->add('createdAt')
            ->add('photo', FileType::class, array('data_class' => null)
                )
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'libelle', 
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
