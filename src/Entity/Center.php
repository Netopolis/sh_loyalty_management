<?php

namespace App\Entity;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CenterRepository")
 * @ORM\Table(name="sgl_centers")
 */
class Center
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=254)
     */
    private $email;

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
     * @ORM\Column(type="integer", length=3)
     */
    private $centerCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoyaltyCard", mappedBy="center")
     */
    private $cardsEmitted;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="center", fetch="EAGER")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer", mappedBy="preferredCenter")
     */
    private $customers;

    private $slug;

    private $phone_format;

    /**
     * @ORM\Column(type="text", nullable=true)
	 * @Assert\File(maxSize="6000000")
     */
    private $centerImage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;


    public function __construct()
    {
        $this->cardsEmitted = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->customers = new ArrayCollection();
    }

    // End properties declarations
    // Begin getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getCenterCode(): ?int
    {
        return $this->centerCode;
    }

    public function setCenterCode(int $centerCode): self
    {
        $this->centerCode = $centerCode;

        return $this;
    }

    /**
     * @return Collection|LoyaltyCard[]
     */
    public function getCardsEmitted(): Collection
    {
        return $this->cardsEmitted;
    }

    public function addCardsEmitted(LoyaltyCard $cardsEmitted): self
    {
        if (!$this->cardsEmitted->contains($cardsEmitted)) {
            $this->cardsEmitted[] = $cardsEmitted;
            $cardsEmitted->setCenter($this);
        }

        return $this;
    }

    public function removeCardsEmitted(LoyaltyCard $cardsEmitted): self
    {
        if ($this->cardsEmitted->contains($cardsEmitted)) {
            $this->cardsEmitted->removeElement($cardsEmitted);
            // set the owning side to null (unless already changed)
            if ($cardsEmitted->getCenter() === $this) {
                $cardsEmitted->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCenter($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCenter() === $this) {
                $user->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setPreferredCenter($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getPreferredCenter() === $this) {
                $customer->setPreferredCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        $this->slug = Transliterator::transliterate($this->name);
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getPhone_Format()
    {
        $this->phone_format =  wordwrap($this->phone, 2, " ", true);
        return $this->phone_format;
    }

    /**
     * @param mixed $phone_format
     */
    public function setPhone_Format($phone_format): void
    {
        $this->phone_format = $phone_format;
    }

	/**
     * @return mixed $centerImage
     */
    public function getCenterImage()
    {
        return $this->centerImage;
    }
	
	/**
     * @param mixed $centerImage
     */
    public function setCenterImage($centerImage)
    {
        $this->centerImage = $centerImage;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

}
