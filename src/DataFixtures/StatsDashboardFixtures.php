<?php
/**
 * Created by Hugues
 */

namespace App\DataFixtures;


use App\Service\YamlStatsProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class StatsDashboardFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $stats = array(); // the array to collect various stats

        // % of satisfied customers
        $satisfaction = mt_rand(79, 87) .'%';
        $stats['satisfaction'] = $satisfaction;


        // totalGamesPlayed, for each center :
        // at least 4 games per day (mt_rand(4, 11) * 7 days * 52 weeks per year * 3,5 years (oldest possible registration date)
        // then add total for each center = totalGamesPlayed since beginning of activity
        $totalGamesPlayed = 0;
        for ($i = 1; $i <= $GLOBALS['count_centers']; $i++) {
            $gamesPlayedAtCenter = mt_rand(4, 11) * 7 * 52 * (mt_rand(21, 35) /10); // the last part reflects the opening of the center from now, in years
            $totalGamesPlayed += (int) floor($gamesPlayedAtCenter);
        }
        $stats['totalGamesPlayed'] = $totalGamesPlayed;

        // likes on facebook
        // each game pits at least 6 to 12 players, but some are returning customers,
        // and only a fraction of all players will post a like
        $likes = (int) floor($totalGamesPlayed * mt_rand(2, 4) * (mt_rand(45, 70) / 100));
        $stats['likes'] = $likes;

        // sales revenue - completely random between 4.26 and 20%
        $salesRevenue = mt_rand(426, 2000) / 100;
        $stats['salesRevenue'] = '+' . $salesRevenue .'%';

        // frequentation, or attendance increase - completely random between 2.26 and 7.82%
        $attendance = mt_rand(226, 782) / 100;
        $stats['attendance'] = '+' . $attendance . '%';


        $yamlStatsProvider = new YamlStatsProvider;
        $yamlStatsProvider->setStats($stats);

    }


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CenterFixtures::class
        ];
    }
}