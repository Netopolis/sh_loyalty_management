<?php

namespace App\Tests\Entity;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\CustomerActivity;
use App\Entity\LoyaltyCard;
use App\Entity\LoyaltyCardRequest;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{

    public $customer;
    private $firstName;
    private $lastName;
    private $nickname;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $zipCode;
    private $city;
    private $country;
    private $customerCode;
    private $registrationDate;
    private $birthDate;
    private $cards;
    private $loyaltyCardRequest;
    private $customerActivity;
    private $isActive;
    private $roles = [];
    private $preferredCenter;
    private $imageProfile;


    public function testFirstName()
    {
		$customer = new Customer();
        $customer->setFirstName('Monsieur X');
        $this->assertSame('Monsieur X', $customer->getFirstName());
    }


    public function testLastName()
    {
		$customer = new Customer();
        $customer->setLastName('Mathieu');
        $this->assertSame('Mathieu', $customer->getLastName());
    }

    public function testNickname()
    {
        $customer = new Customer();
        $customer->setNickname('MXC');
        $this->assertSame('MXC', $customer->getNickname());
    }

    public function testEmail()
    {
        $customer = new Customer();
        $customer->setEmail('x_monsieur@gmail.com');
        $this->assertSame('x_monsieur@gmail.com', $customer->getEmail());
    }

    public function testPassword()
    {
        $customer = new Customer();
        $customer->setPassword('ABCDEFGH56');
        $this->assertSame('ABCDEFGH56', $customer->getPassword());
    }

    public function testPhone()
    {
        $customer = new Customer();
        $customer->setPhone('0123456789');
        $this->assertSame('0123456789', $customer->getPhone());
    }


    public function testAddress()
    {
        $customer = new Customer();
        $customer->setAddress('2 rue des Blooms');
        $this->assertSame('2 rue des Blooms', $customer->getAddress());
    }


    public function testZipCode()
    {
        $customer = new Customer();
        $customer->setZipCode('75020');
        $this->assertSame('75020', $customer->getZipCode());
    }


    public function testCity()
    {
        $customer = new Customer();
        $customer->setCity('New York');
        $this->assertSame('New York', $customer->getCity());
    }


    public function testCountry()
    {
        $customer = new Customer();
        $customer->setCountry('Francia');
        $this->assertSame('Francia', $customer->getCountry());
    }


    public function testCustomerCode()
    {
        $customer = new Customer();
        $customer->setCity('New York');
        $this->assertSame('New York', $customer->getCity());
    }


    public function testRegistrationDate()
    {
        $customer = new Customer();
		$d = new \DateTime('2018-01-03');
        $customer->setRegistrationDate($d);
        $this->assertSame($d, $customer->getRegistrationDate());
    }


    public function testBirthDate()
    {
        $customer = new Customer();
		$d = new \DateTime('2008-05-03');
        $customer->setBirthDate($d);
        $this->assertSame($d, $customer->getBirthDate());
    }

    public function testCards()
    {
        $customer = new Customer();
		$card = new LoyaltyCard();
		$card->setCardCode(123);
        $customer->addCard($card);
		
        $this->assertSame(123, $customer->getCards()[0]->getCardCode());

        $customer->removeCard($card);
        $this->assertEmpty($customer->getCards());

    }

    public function testCardRequest()
    {
        $customer = new Customer();
		$card_q = new LoyaltyCardRequest();
		$card_q->setStatus(2);
        $customer->addCardRequest($card_q);
        $this->assertSame(2, $customer->getCardRequest()[0]->getStatus());

        $customer->removeCardRequest($card_q);
        $this->assertEmpty($customer->getCardRequest());
    }

    public function testCustomerActivity()
    {
        $customer = new Customer();
		$ca = new CustomerActivity();
        $customer->setCustomerActivity($ca);
        $this->assertSame($ca, $customer->getCustomerActivity());
    }


    public function testIsActive()
    {
        $customer = new Customer();
        $customer->setIsActive(true);
        $this->assertSame(true, $customer->getIsActive());
    }


    public function testRoles()
    {
        $customer = new Customer();
        $customer->setRoles(array('ROLE_USER'));
        $this->assertSame(array('ROLE_USER'), $customer->getRoles());
    }

    public function testPreferredCenter()
    {
        $customer = new Customer();
		$center = new Center();
        $customer->setPreferredCenter($center);
        $this->assertSame($center, $customer->getPreferredCenter());
    }

    public function testImageProfile()
    {
        $customer = new Customer();
        $customer->setImageProfile('image_p');
        $this->assertSame('image_p', $customer->getImageProfile());
    }

    public function testSalt()
    {
        $customer = new Customer();
        $this->assertNull($customer->getSalt());
    }

    public function testUsername()
    {
        $customer = new Customer();
        $customer->setEmail('x_monsieur@gmail.com');
        $this->assertSame('x_monsieur@gmail.com', $customer->getUsername());
    }

}
