<?php
/**
 * Created by Hugues
 * 06/01/2019
 */

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\CustomerActivity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class CustomerActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

        // Create customers activities - based on the number of generated customers, $GLOBALS['count_customers']
        // customerActivity is already initialized in the customer constructor, with empty values. What we need are references
        // loop through the customers
        for ($i = 1; $i <= $GLOBALS['count_customers']; $i++) {
            $customer = $this->getReference('customer' . $i);
            $activity = new CustomerActivity();
            $activityId = $customer->getCustomerActivity()->getId();
            // fetch back the object
            $activity = $manager->find(CustomerActivity::class, $activityId);

            // calculate number of weeks since customer registration - this will be very helpful later
            $registrationDate = $customer->getRegistrationDate();
            $registrationDate = date_format($registrationDate, 'm/d/Y');
            // using dateDiffInWeeks to return the number of weeks between registration date and now
            $today = date('m/d/Y');
            $weeks = $helper->dateDiffInWeeks($registrationDate, $today);


            // Let's set all the stats' values

            // Starting with a secret factor, the player's skill
            // and rounding it to a float with 2 precision - between 0.85 and 1.25
            $playerSkill = round(($helper->randomFloat(0.85, 1.25)), 2);

            // Number of games played: we assume that the player has played between 0.4 to 3 times per week
            // multiplied by $weeks, and rounded up
            // ceil rounds to the next highest integer, but is still a float - we need to cast it
            $gamesPlayed = (int) ceil(($helper->randomFloat(0.4, 3) * $weeks));
            $activity->setGamesPlayed($gamesPlayed);

            // games won is a random number of $gamesPlayed, multiplied by player skill
            // The chance to win is 50% when there are 2 teams, actually less when there are 3 teams. We default it to 36-60% multiplied by $playerSkill
            $gamesWon = $gamesPlayed * round((($helper->randomFloat(0.36, 0.6)) * $playerSkill), 2);
            $gamesWon = (int) ceil($gamesWon);
            $activity->setGamesWon($gamesWon);

            // solo victories are much rarer than team victories: most of the time the game is played in teams
            // though there is always a better player - default 30-50% of $gamesWon
            $soloVictories = (int) ceil($gamesWon * ($helper->randomFloat(0.3, 0.5)));
            $activity->setSoloVictories($soloVictories);

            // team victories is the rest of games won
            $teamVictories = $gamesWon - $soloVictories;
            $activity->setTeamVictories($teamVictories);

            // each center organizes 2 tournaments per year (every 26 weeks)
            // so a player can enter a maximum of 2 * $GLOBALS['count_centers'] tournaments
            // Let's get the number of tournaments since the player's registration
            $tournamentsOrganized = (int) floor(($weeks / 26));
            $tournamentsOrganized = $tournamentsOrganized * $GLOBALS['count_centers']; // multiply by number of centers
            // A player has a flat 50-65% chance of entering tournaments
            if ((mt_rand(1, 100)) <= (mt_rand(50, 65))) {
                // if he does, he will compete in 20-65% of all tournaments
                $tournamentsPlayed = (int) ceil((($tournamentsOrganized * mt_rand(20, 65)) / 100));
                $activity->setTournamentsPlayed($tournamentsPlayed);

                // Then a player has a random 15-25% chance of winning a tournament, affected by player skill
                $winningChance = mt_rand(15, 25) * $playerSkill;
                $tournamentsWon = (int) floor(($tournamentsPlayed * $winningChance) / 100);
                $activity->setTournamentsWon($tournamentsWon);
            }

            // max consecutive games won is a random value, affected by player skill
            if($playerSkill < 1.00) {
                $maxConsecutiveGamesWon = (int) ceil((mt_rand(1, 6) * $playerSkill));
            }
            else {
                // $playerSkill is > 1
                $maxConsecutiveGamesWon = (int) ceil((mt_rand(1, 9) * $playerSkill));
            }
            $activity->setMaxConsecutiveGamesWon($maxConsecutiveGamesWon);

            // Average successful hits par Game: each game last 20 minutes, and each player fires 11 to 32 shots per minute, that's between 220 and 640 shots per game
            $shotsPerGame = mt_rand(220, 640);
            // base chance is affected by player skill
            $baseHitChance = mt_rand(32 , 48) * $playerSkill;
            $averageHitsPerGame = ($shotsPerGame * $baseHitChance) / 100;
            $activity->setAverageHitsPerGame($averageHitsPerGame);

            // Average misses par game is total shots - hits
            $averageMissesPerGame = $shotsPerGame - $averageHitsPerGame;
            $activity->setAverageMissesPerGame($averageMissesPerGame);

            // Total points all time: Points per game are 100 per hit - random points (50 * 201-442)
            // Let's loop through games Played
            $totalPointsAllTime = 0;
            for ($j = 1; $j <= $gamesPlayed; $j++) {
                $gamePoints = ($averageHitsPerGame * 100) - (50 * mt_rand(201, 442));
                if ($gamePoints < 0) {$gamePoints = 0;}
                $totalPointsAllTime += $gamePoints;
            }
            $activity->setTotalPointsAllTime($totalPointsAllTime);

            // Average points per game
            $averagePointsPerGame = floor($totalPointsAllTime / $gamesPlayed);
            $activity->setAveragePointsPerGame($averagePointsPerGame);

            // Friend invited to games : loop through the games played and add a random chance for friends
            $friendsInvited = 0;
            for ($j = 1; $j <= $gamesPlayed; $j++) {
                // 65% chance of inviting friends
                if ((mt_rand(1, 100)) <= 65) {
                    $friendsInvited += mt_rand(0, 4);
                }
            }
            $activity->setFriendsInvitedToGames($friendsInvited);

            // Customers sponsored: low chance among friends invited
            if ((mt_rand(1, 100)) <= 25) {
                $customersSponsored = mt_rand(1, 7);
            } else {
                $customersSponsored = 0;
            }
            $activity->setCustomersSponsored($customersSponsored);

            // Total Spending all Time: number of games played, average 9 Euros, minus discounts for loyalty
            $totalSpendingAllTime = (9 * $gamesPlayed) * ($helper->randomFloat(0.82, 0.95));
            $totalSpendingAllTime = round($totalSpendingAllTime, 2);
            $activity->setTotalSpendingAllTime($totalSpendingAllTime);

            // Average spending per month: total spending divided per (weeks / 4)
            $averageSpendingPerMonth = round(($totalSpendingAllTime / ($weeks /4)), 2);
            $activity->setAverageSpendingPerMonth($averageSpendingPerMonth);

            // Total activities all time
            $activity->setTotalActivitiesAllTime($gamesPlayed);

            // Average activities par month
            $averageActivitiesPerMonth = (int) floor(($gamesPlayed / ($weeks /4)));
            $activity->setAverageActivitiesPerMonth($averageActivitiesPerMonth);


            // Last activity, generate a random date
            $startDate = mt_rand(5, 8);
            $endDate = mt_rand(2, 7);
            $lastActivity = $helper->generateRandDate('-'.$startDate . ' weeks', '-'.$endDate . ' days');
            $activity->setLastActivity($lastActivity);

            $manager->persist($activity);
        }


        $manager->flush();
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
            CenterFixtures::class,
            CustomerFixtures::class
        ];
    }
}
