<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerActivityRepository")
 * @ORM\Table(name="sgl_customer_activity")
 */
class CustomerActivity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer", inversedBy="customerActivity", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gamesPlayed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gamesWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $soloVictories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $teamVictories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tournamentsPlayed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tournamentsWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxConsecutiveGamesWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $averageMissesPerGame;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $averageHitsPerGame;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $averagePointsPerGame;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalPointsAllTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $friendsInvitedToGames;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $customersSponsored;

    /**
     * @ORM\Column(type="float", scale=2, nullable=true)
     */
    private $averageSpendingPerMonth;

    /**
     * @ORM\Column(type="float", scale=2, nullable=true)
     */
    private $totalSpendingAllTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $averageActivitiesPerMonth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalActivitiesAllTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastActivity;


    // End properties declarations
    // Begin getters and setters

    public function __construct()
    {
        $this->gamesPlayed = 0;
        $this->gamesWon = 0;
        $this->soloVictories = 0;
        $this->teamVictories = 0;
        $this->tournamentsPlayed = 0;
        $this->tournamentsWon = 0;
        $this->maxConsecutiveGamesWon = 0;
        $this->averageMissesPerGame = 0;
        $this->averageHitsPerGame = 0;
        $this->averagePointsPerGame = 0;
        $this->totalPointsAllTime = 0;
        $this->friendsInvitedToGames = 0;
        $this->customersSponsored = 0;
        $this->averageSpendingPerMonth = 0;
        $this->totalSpendingAllTime = 0;
        $this->tournamentsPlayed = 0;
        $this->averageActivitiesPerMonth = 0;
        $this->totalActivitiesAllTime = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getGamesPlayed(): ?int
    {
        return $this->gamesPlayed;
    }

    public function setGamesPlayed(?int $gamesPlayed): self
    {
        $this->gamesPlayed = $gamesPlayed;

        return $this;
    }

    public function getGamesWon(): ?int
    {
        return $this->gamesWon;
    }

    public function setGamesWon(?int $gamesWon): self
    {
        $this->gamesWon = $gamesWon;

        return $this;
    }

    public function getSoloVictories(): ?int
    {
        return $this->soloVictories;
    }

    public function setSoloVictories(?int $soloVictories): self
    {
        $this->soloVictories = $soloVictories;

        return $this;
    }

    public function getTeamVictories(): ?int
    {
        return $this->teamVictories;
    }

    public function setTeamVictories(?int $teamVictories): self
    {
        $this->teamVictories = $teamVictories;

        return $this;
    }

    public function getTournamentsPlayed(): ?int
    {
        return $this->tournamentsPlayed;
    }

    public function setTournamentsPlayed(?int $tournamentsPlayed): self
    {
        $this->tournamentsPlayed = $tournamentsPlayed;

        return $this;
    }

    public function getTournamentsWon(): ?int
    {
        return $this->tournamentsWon;
    }

    public function setTournamentsWon(?int $tournamentsWon): self
    {
        $this->tournamentsWon = $tournamentsWon;

        return $this;
    }

    public function getMaxConsecutiveGamesWon(): ?int
    {
        return $this->maxConsecutiveGamesWon;
    }

    public function setMaxConsecutiveGamesWon(?int $maxConsecutiveGamesWon): self
    {
        $this->maxConsecutiveGamesWon = $maxConsecutiveGamesWon;

        return $this;
    }

    public function getAverageMissesPerGame(): ?int
    {
        return $this->averageMissesPerGame;
    }

    public function setAverageMissesPerGame(?int $averageMissesPerGame): self
    {
        $this->averageMissesPerGame = $averageMissesPerGame;

        return $this;
    }

    public function getAverageHitsPerGame(): ?int
    {
        return $this->averageHitsPerGame;
    }

    public function setAverageHitsPerGame(?int $averageHitsPerGame): self
    {
        $this->averageHitsPerGame = $averageHitsPerGame;

        return $this;
    }

    public function getAveragePointsPerGame(): ?int
    {
        return $this->averagePointsPerGame;
    }

    public function setAveragePointsPerGame(?int $averagePointsPerGame): self
    {
        $this->averagePointsPerGame = $averagePointsPerGame;

        return $this;
    }

    public function getTotalPointsAllTime(): ?int
    {
        return $this->totalPointsAllTime;
    }

    public function setTotalPointsAllTime(?int $totalPointsAllTime): self
    {
        $this->totalPointsAllTime = $totalPointsAllTime;

        return $this;
    }

    public function getFriendsInvitedToGames(): ?int
    {
        return $this->friendsInvitedToGames;
    }

    public function setFriendsInvitedToGames(?int $friendsInvitedToGames): self
    {
        $this->friendsInvitedToGames = $friendsInvitedToGames;

        return $this;
    }

    public function getCustomersSponsored(): ?int
    {
        return $this->customersSponsored;
    }

    public function setCustomersSponsored(?int $customersSponsored): self
    {
        $this->customersSponsored = $customersSponsored;

        return $this;
    }

    public function getAverageSpendingPerMonth(): ?float
    {
        return $this->averageSpendingPerMonth;
    }

    public function setAverageSpendingPerMonth(?float $averageSpendingPerMonth): self
    {
        $this->averageSpendingPerMonth = $averageSpendingPerMonth;

        return $this;
    }

    public function getTotalSpendingAllTime(): ?float
    {
        return $this->totalSpendingAllTime;
    }

    public function setTotalSpendingAllTime(?float $totalSpendingAllTime): self
    {
        $this->totalSpendingAllTime = $totalSpendingAllTime;

        return $this;
    }

    public function getAverageActivitiesPerMonth(): ?int
    {
        return $this->averageActivitiesPerMonth;
    }

    public function setAverageActivitiesPerMonth(?int $averageActivitiesPerMonth): self
    {
        $this->averageActivitiesPerMonth = $averageActivitiesPerMonth;

        return $this;
    }

    public function getTotalActivitiesAllTime(): ?int
    {
        return $this->totalActivitiesAllTime;
    }

    public function setTotalActivitiesAllTime(?int $totalActivitiesAllTime): self
    {
        $this->totalActivitiesAllTime = $totalActivitiesAllTime;

        return $this;
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(?\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }
}
