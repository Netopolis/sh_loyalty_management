<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\Customer;
use App\Repository\CenterRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{

    /**
     * @var CenterRepository
     */
    private $centerRepo;

    /**
     * CustomerType constructor.
     * @param CenterRepository $centerRepo
     */
    public function __construct(CenterRepository $centerRepo)
    {
        $this->centerRepo = $centerRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerCode',TextType::class,[
                'label' => 'Code client',
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('preferredCenter', EntityType::class, [
                'class' => Center::class,
                'label' => 'Centre de rattachement',
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'query_builder' => $this->centerRepo->createPublishedCenterQueryBuilder()
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
//            ->add('remove_password', CheckboxType::class, [
//                'label' => 'Mettre un mot de passe vide',
//                'mapped' => false,
//                'required' => false
//            ])
            ->addEventSubscriber(new KeepValueListener('password', 'remove_password'))
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
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
