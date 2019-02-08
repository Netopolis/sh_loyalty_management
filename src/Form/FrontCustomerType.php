<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrontCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerCode',HiddenType::class,[
                'label' => 'Code client'
            ])
            ->add('firstName',TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('lastName',TextType::class,[
                'label' => 'Nom'
            ])
            ->add('nickname',TextType::class,[
                'label' => 'Surnom',
                'required' => false
            ])
            ->add('imageProfile',FileType::class, [
                'label' => 'Image du profil',
                'required' => false,
                'attr' => [
                    'class' => 'dropify',
                    'data-default-file' => $options['image_url']
                ]
            ])
            ->add('email',EmailType::class,[
                'label' => 'Email'
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs mot de passe doivent correspondre.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer votre mot de passe'),
            ))
            /*
            ->add('password',PasswordType::class,[
                'label' => 'Mot de passe',
                'always_empty' => false,
                'mapped' => true,
                'required' => false,
                'help' => 'Laissez ce champ vide si vous ne souhaitez rien changer'
            ])
            ->add('remove_password', HiddenType::class, [
                'label' => 'Mettre un mot de passe vide',
                'mapped' => false,
                'required' => false
            ])*/
            /*->addEventSubscriber(new KeepValueListener('password', 'remove_password'))*/
            ->add('phone',TextType::class,[
                'label' => 'Téléphone',
                'required' => false
            ])
            ->add('address',TextType::class,[
                'label' => 'Adresse'
            ])
            ->add('zipCode',TextType::class,[
                'label' => 'Code postal'
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville'
            ])
            ->add('country',TextType::class,[
                'label' => 'Pays'
            ])
            ->add('birthDate',BirthdayType::class,[
                'label' => 'Date de naissance',
                'required' => false
            ])
            ->add('preferredCenter', EntityType::class, [
                'class' => Center::class,
                'label' => 'Choisir un centre de rattachement',
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'image_url' => null,
			'image_name' => null
        ]);
    }
}
