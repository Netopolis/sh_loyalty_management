<?php

namespace App\Form;

use App\Entity\Center;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('centerCode',TextType::class,[
                'label' => 'Code centre',
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('name',TextType::class, [
                'label' => 'Enseigne'
            ])
            ->add('phone',TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('email',EmailType::class, [
                'label' => 'Email'
            ])
            ->add('address',TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('zipCode',TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('city',TextType::class, [
                'label' => 'Ville'
            ])
            ->add('country',TextType::class, [
                'label' => 'Pays'
            ])
            ->add('centerImage',FileType::class, [
                'label' => false,
				'required' => false,
                'attr' => [
                    'class' => 'dropify',
                    'data-default-file' => $options['image_url']
                ]
            ])
            ->add('published',CheckboxType::class, [
                'label' => 'Publier le centre',
				'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Center::class,
            'image_url' => null,
			'image_name' => null
        ]);
    }
}
