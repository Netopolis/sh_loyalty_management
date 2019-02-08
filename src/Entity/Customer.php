<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @UniqueEntity(fields="email", message="Cet Email est déjà enregistré")
 * @ORM\Table(name="sgl_customers")
 */
class Customer implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=140)
     * @OrderBy({"name" = "ASC"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=18)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=189)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=90)
     */
    private $country;

    /**
     * @ORM\Column(type="integer", length=6)
     */
    private $customerCode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoyaltyCard", mappedBy="customer", fetch="EAGER")
     */
    private $cards;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoyaltyCardRequest", mappedBy="customer", orphanRemoval=true, fetch="EAGER")
     */
    private $loyaltyCardRequest;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CustomerActivity", mappedBy="customer", fetch="EAGER", cascade={"persist", "remove"})
     */
    private $customerActivity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Center", inversedBy="customers")
     */
    private $preferredCenter;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $imageProfile;


    // End properties declarations
    // Begin getters and setters

    public function __construct()
    {
        $this->registrationDate = new \DateTime();
        $this->cards = new ArrayCollection();
        $this->customerActivity = new CustomerActivity();
        $this->customerActivity->setCustomer($this);
        $this->isActive = true;
        $this->roles = array('ROLE_USER');
        $this->loyaltyCardRequest = new ArrayCollection();
        $this->imageProfile = "profil_default_image.png";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCustomerCode(): ?int
    {
        return $this->customerCode;
    }

    public function setCustomerCode(int $customerCode): self
    {
        $this->customerCode = $customerCode;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Collection|LoyaltyCard[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(LoyaltyCard $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setCustomer($this);
        }

        return $this;
    }

    public function removeCard(LoyaltyCard $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            // set the owning side to null (unless already changed)
            if ($card->getCustomer() === $this) {
                $card->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LoyaltyCardRequest[]
     */
    public function getCardRequest(): Collection
    {
        return $this->loyaltyCardRequest;
    }

    public function addCardRequest(loyaltyCardRequest $loyaltyCardRequest): self
    {
        if (!$this->loyaltyCardRequest->contains($loyaltyCardRequest)) {
            $this->loyaltyCardRequest[] = $loyaltyCardRequest;
            $loyaltyCardRequest->setCustomer($this);
        }

        return $this;
    }

    public function removeCardRequest(LoyaltyCardRequest $loyaltyCardRequest): self
    {
        if ($this->loyaltyCardRequest->contains($loyaltyCardRequest)) {
            $this->loyaltyCardRequest->removeElement($loyaltyCardRequest);
            // set the owning side to null (unless already changed)
            if ($loyaltyCardRequest->getCustomer() === $this) {
                $loyaltyCardRequest->setCustomer(null);
            }
        }

        return $this;
    }

    public function getCustomerActivity(): ?CustomerActivity
    {
        return $this->customerActivity;
    }

    public function setCustomerActivity(CustomerActivity $customerActivity): self
    {
        $this->customerActivity = $customerActivity;

        // set the owning side of the relation if necessary
        if ($this !== $customerActivity->getCustomer()) {
            $customerActivity->setCustomer($this);
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPreferredCenter(): ?Center
    {
        return $this->preferredCenter;
    }

    public function setPreferredCenter(?Center $preferredCenter): self
    {
        $this->preferredCenter = $preferredCenter;

        return $this;
    }

    /**
     * Used for forms when it is more convenient to display the full name on a single line
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getImageProfile()
    {
        return $this->imageProfile;
    }

    /**
     * @param mixed $imageProfile
     */
    public function setImageProfile($imageProfile): void
    {
        $this->imageProfile = $imageProfile;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->getId(),
            $this->getEmail(),
            $this->getPassword()
        ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password) = unserialize($serialized);
    }
}
