<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoyaltyCardRepository")
 * @ORM\Table(name="sgl_loyalty_cards")
 */
class LoyaltyCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $cardCode;

    /**
     * @ORM\Column(type="string", length=340, nullable=true)
     */
    private $QRCode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateOfIssue;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPhoneAppActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $loyaltyPoints;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="cards", fetch="EAGER")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Center", inversedBy="cardsEmitted")
     */
    private $center;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;

    
    /**
     * LoyaltyCard constructor.
     */
    public function __construct()
    {
        $this->dateOfIssue = new \DateTime();
        $this->isValid = true;
        $this->isPhoneAppActive = false;
        $this->loyaltyPoints = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardCode(): ?int
    {
        return $this->cardCode;
    }

    public function setCardCode(int $cardCode): self
    {
        $this->cardCode = $cardCode;

        return $this;
    }

    public function getQRCode(): ?string
    {
        return $this->QRCode;
    }

    public function setQRCode(?string $QRCode): self
    {
        $this->QRCode = $QRCode;

        return $this;
    }

    public function getDateOfIssue(): ?\DateTimeInterface
    {
        return $this->dateOfIssue;
    }

    public function setDateOfIssue(\DateTimeInterface $dateOfIssue): self
    {
        $this->dateOfIssue = $dateOfIssue;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getIsPhoneAppActive(): ?bool
    {
        return $this->isPhoneAppActive;
    }

    public function setIsPhoneAppActive(bool $isPhoneAppActive): self
    {
        $this->isPhoneAppActive = $isPhoneAppActive;

        return $this;
    }

    public function getLoyaltyPoints(): ?int
    {
        return $this->loyaltyPoints;
    }

    public function setLoyaltyPoints(int $loyaltyPoints): self
    {
        $this->loyaltyPoints = $loyaltyPoints;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCenter(): ?Center
    {
        return $this->center;
    }

    public function setCenter(?Center $center): self
    {
        $this->center = $center;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
