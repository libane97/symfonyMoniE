<?php

namespace App\Form;

use App\Entity\Commune;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommuneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => ['placeholder' => 'Veuillez saisir le nom de la zone',]
            ])
            ->add('tarif' , TextType::class, [
                'attr' => ['placeholder' => 'Veuillez saisir le tarif de la zone',]
            ])
            ->add('montantMax', TextType::class, [
                'attr' => ['placeholder' => 'Veuillez saisir le montant max de la zone pour la livraison',]
            ])
            ->add('Enregister', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commune::class,
        ]);
    }
}
