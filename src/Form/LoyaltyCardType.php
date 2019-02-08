<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoyaltyCardType extends AbstractType
{
    private $em;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Let's start with a form event, to check if we are creating a new card, or editing one
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $loyaltyCard = $event->getData();
            $form = $event->getForm();


            // If no data is passed to the form, the data is "null",
            // Then it must be a new Card
            if (!$loyaltyCard || null === $loyaltyCard->getId()) {

                $form
                    ->add('center', EntityType::class, [
                        'class' => Center::class,
                        'label' => 'Centre émetteur',
                        'choice_label' => 'name',
                        'expanded' => false,
                        'multiple' => false,
                    ])
                    ->add('customer', EntityType::class, [
                        'class' => Customer::class,
                        'label' => 'Client',
                        /*'choices' => $users,*/
                        'choice_label' => function (Customer $customer) {
                            return $customer->getFullName();
                        },

                        'query_builder' => function (CustomerRepository $v) {

                            return $v->CustomersWithoutCard();
                        },
                        'expanded' => false,
                        'multiple' => false,
                    ])
                    ->add('cardCode', TextType::class, [
                        'label' => 'Code carte',
                        'attr' => [
                            'readonly' => true,
                            'required' => false,
                            'mapped' => false,
                            'placeholder' => 'Le Code carte sera généré après la demande'
                        ]
                    ])
                    ->add('QRCode', TextType::class, [
                        'label' => 'QRCode',
                        'attr' => [
                            'readonly' => true,
                            'required' => false,
                            'mapped' => false,
                            'placeholder' => 'Le QRCode sera généré après la demande'
                        ]
                    ])
                    ->add('dateOfIssue', DateType::class, [
                        'label' => 'Date d\'émission'
                    ]);
            } else {

                // We are editing an existing card, so we need to change several fields and add a few
                // Some fields must be set as 'disabled' => true (and NOT attr => [ 'disabled' => true ], or attr => [ 'readonly' => true ], both still allow tampering with the data)
                // Because once a card is created, you can't change its center nor its customer,
                // this would invalidate the card code

                $form
                    ->add('center', EntityType::class, [
                        'class' => Center::class,
                        'label' => 'Centre émetteur',
                        'choice_label' => 'name',
                        'expanded' => false,
                        'multiple' => false,
                        'disabled' => true
                    ])
                    ->add('customer', EntityType::class, [
                        'class' => Customer::class,
                        'label' => 'Propriétaire',
                        'choice_label' => function (Customer $customer) {
                            return $customer->getFullName();
                        },
                        'query_builder' => function (CustomerRepository $v) {
                            return $v->createQueryBuilder('c')
                                ->orderBy('c.lastName', ' ASC');
                        },
                        'expanded' => false,
                        'multiple' => false,
                        'disabled' => true
                    ])
                    ->add('cardCode', TextType::class, [
                        'label' => 'Code carte',
                        'disabled' => true
                    ])
                    ->add('QRCode', TextType::class, [
                        'label' => 'QRCode',
                        'required' => false,
                        'attr' => [
                            'readonly' => true
                        ]
                    ])
                    ->add('loyaltyPoints', IntegerType::class, [
                        'label' => 'Points de fidélité'
                    ])
                    ->add('dateOfIssue', DateType::class, [
                        'label' => 'Date d\'émission'
                    ])
                    ->add('isValid', CheckboxType::class, [
                        'label' => 'Valide ?',
                        'required' => false
                    ]);
            }
        });
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoyaltyCard::class
        ]);
    }
}
