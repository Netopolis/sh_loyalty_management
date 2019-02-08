<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use App\Entity\CustomerActivity;
use PHPUnit\Framework\TestCase;


class CustomerActivityTest extends TestCase
{

    public $customerActivity;
    private $customer;
    private $gamesPlayed;
    private $gamesWon;
    private $soloVictories;
    private $teamVictories;
    private $tournamentsPlayed;
    private $tournamentsWon;
    private $maxConsecutiveGamesWon;
    private $averageMissesPerGame;
    private $averageHitsPerGame;
    private $averagePointsPerGame;
    private $totalPointsAllTime;
    private $friendsInvitedToGames;
    private $customersSponsored;
    private $averageSpendingPerMonth;
    private $totalSpendingAllTime;
    private $averageActivitiesPerMonth;
    private $totalActivitiesAllTime;
    private $lastActivity;


    public function testCustomer()
    {
        $customerActivity = new CustomerActivity();
		$customer = new Customer();
        $customerActivity->setCustomer($customer);
        $this->assertSame($customer, $customerActivity->getCustomer());
    }

    public function testGamesPlayed()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setGamesPlayed(9);
        $this->assertSame(9, $customerActivity->getGamesPlayed());
    }

    public function testGamesWon()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setGamesWon(12);
        $this->assertSame(12, $customerActivity->getGamesWon());
    }

    public function testSoloVictories()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setSoloVictories(7);
        $this->assertSame(7, $customerActivity->getSoloVictories());
    }

    public function testTeamVictories()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTeamVictories(56);
        $this->assertSame(56, $customerActivity->getTeamVictories());
    }

    public function testTournamentsPlayed()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTournamentsPlayed(13);
        $this->assertSame(13, $customerActivity->getTournamentsPlayed());
    }

    public function testTournamentsWon()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTournamentsWon(11);
        $this->assertSame(11, $customerActivity->getTournamentsWon());
    }

    public function testMaxConsecutiveGamesWon()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setMaxConsecutiveGamesWon(4);
        $this->assertSame(4, $customerActivity->getMaxConsecutiveGamesWon());
    }

    public function testAverageMissesPerGame()
    {
         $customerActivity = new CustomerActivity();
        $customerActivity->setAverageMissesPerGame(23);
        $this->assertSame(23, $customerActivity->getAverageMissesPerGame());
    }

    public function testAverageHitsPerGame()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setAverageHitsPerGame(21);
        $this->assertSame(21, $customerActivity->getAverageHitsPerGame());
    }

    public function testAveragePointsPerGame()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setAveragePointsPerGame(241);
        $this->assertSame(241, $customerActivity->getAveragePointsPerGame());
    }

    public function testTotalPointsAllTime()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTotalPointsAllTime(8241);
        $this->assertSame(8241, $customerActivity->getTotalPointsAllTime());
    }

    public function testFriendsInvitedToGames()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setFriendsInvitedToGames(3);
        $this->assertSame(3, $customerActivity->getFriendsInvitedToGames());
    }

    public function testCustomersSponsored()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setCustomersSponsored(7);
        $this->assertSame(7, $customerActivity->getCustomersSponsored());
    }

    public function testAverageSpendingPerMonth()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setAverageSpendingPerMonth(72.2);
        $this->assertSame(72.2, $customerActivity->getAverageSpendingPerMonth());
    }

    public function testTotalSpendingAllTime()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTotalSpendingAllTime(173.5);
        $this->assertSame(173.5, $customerActivity->getTotalSpendingAllTime());
    }

    public function testAverageActivitiesPerMonth()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setAverageActivitiesPerMonth(83);
        $this->assertSame(83, $customerActivity->getAverageActivitiesPerMonth());
    }

    public function testTotalActivitiesAllTime()
    {
        $customerActivity = new CustomerActivity();
        $customerActivity->setTotalActivitiesAllTime(479);
        $this->assertSame(479, $customerActivity->getTotalActivitiesAllTime());
    }

    public function testLastActivity()
    {
        $customerActivity = new CustomerActivity();
        $dateTime = new \DateTime('NOW');
        $customerActivity->setLastActivity($dateTime);
        $this->assertSame($dateTime, $customerActivity->getLastActivity());
    }

}
