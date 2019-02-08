<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRequestRepository")
 * @ORM\Table(name="sgl_loyalty_cards_requests")
 */
class LoyaltyCardRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="loyaltyCardRequest", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateOfRequest;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;


    /**
     * LoyaltyCardRequest constructor.
     */
    public function __construct()
    {
        $this->dateOfRequest = new \DateTime();
        $this->setStatus(0);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateOfRequest(): ?\DateTimeInterface
    {
        return $this->dateOfRequest;
    }

    public function setDateOfRequest(\DateTimeInterface $dateOfRequest): self
    {
        $this->dateOfRequest = $dateOfRequest;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
