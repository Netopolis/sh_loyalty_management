<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\User;
use App\Form\KeepValueListener;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('center', EntityType::class, [
                'class' => Center::class,
                'label' => 'Centre de rattachement',
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'required' => false
            ])
            ->add('firstName',TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('lastName',TextType::class,[
                'label' => 'Nom'
            ])
            ->add('email',EmailType::class,[
                'label' => 'Email'
            ])
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
            ])
            ->addEventSubscriber(new KeepValueListener('password', 'remove_password'))
            ->add('roles', CollectionType::class,[
                'label' => 'Roles',
                'entry_options' => array('label' => false),
                'required' => false
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
