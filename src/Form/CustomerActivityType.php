<?php

namespace App\Form;

use App\Entity\CustomerActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastActivity',DateType::class,[
                'label' => 'Date de dernière activité',
            ])
            ->add('friendsInvitedToGames', IntegerType::class,[
                'label' => 'Nombre de personnes invitées',
            ])
            ->add('customersSponsored',IntegerType::class,[
                'label' => 'Nombre de clients parrainnés',
            ])
            ->add('totalSpendingAllTime', MoneyType::class,[
                'label' => 'Dépenses totales depuis inscription'
            ])
            ->add('averageSpendingPerMonth', MoneyType::class,[
                'label' => 'Dépenses moyennes par mois (calculées auto)',
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('gamesPlayed', IntegerType::class,[
                'label' => 'Partie(s) disputée(s)'
            ])
			->add('gamesWon', IntegerType::class,[
                'label' => 'Partie(s) gagnée(s)'
            ])
			->add('soloVictories', IntegerType::class,[
                'label' => 'Victoire(s) Solo'
            ])
			->add('teamVictories', IntegerType::class,[
                'label' => 'Victoire(s) en Equipe'
            ])
			->add('totalPointsAllTime',IntegerType::class,[
                'label' => 'Point(s) gagné(s)'
            ])
            ->add('tournamentsPlayed',IntegerType::class,[
                'label' => 'Tournoi(s) disputé(s)'
            ])
			->add('tournamentsWon',IntegerType::class,[
                'label' => 'Tournoi(s) gagné(s)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerActivity::class,
        ]);
    }
}
