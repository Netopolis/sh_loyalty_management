<?php

namespace App\Tests\Entity;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use PHPUnit\Framework\TestCase;

class LoyaltyCardTest extends TestCase
{

    public $LoyaltyCard;
    private $cardCode;
    private $QRCode;
    private $dateOfIssue;
    private $isValid;
    private $isPhoneAppActive;
    private $loyaltyPoints;
    private $customer;
    private $center;
    private $status;


    public function testCardCode()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setCardCode(11234);
        $this->assertSame(11234, $LoyaltyCard->getCardCode());
    }


    public function testQRCode()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setQRCode('AB654');
        $this->assertSame('AB654', $LoyaltyCard->getQRCode());
    }

    public function testDateOfIssue()
    {
        $LoyaltyCard = new LoyaltyCard();
		$date = new \DateTime('2000-01-01');
		$date->format('Y-m-d H:i:s');
        $LoyaltyCard->setDateOfIssue($date);
        $this->assertSame($date, $LoyaltyCard->getDateOfIssue());
    }

    public function testIsValid()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setIsValid(true);
        $this->assertSame(true, $LoyaltyCard->getIsValid());
    }

    public function testIsPhoneAppActive()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setIsPhoneAppActive(true);
        $this->assertSame(true, $LoyaltyCard->getIsPhoneAppActive());
    }

    public function testLoyaltyPoints()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setLoyaltyPoints(453);
        $this->assertSame(453, $LoyaltyCard->getLoyaltyPoints());
    }

    public function testCustomer()
    {
        $LoyaltyCard = new LoyaltyCard();
		$customer = new Customer();
        $LoyaltyCard->setCustomer($customer);
        $this->assertSame($customer, $LoyaltyCard->getCustomer());
    }

    public function testCenter()
    {
		$LoyaltyCard = new LoyaltyCard();
		$center = new center();
        $LoyaltyCard->setCenter($center);
        $this->assertSame($center, $LoyaltyCard->getCenter());
    }


    public function testStatus()
    {
        $LoyaltyCard = new LoyaltyCard();
        $LoyaltyCard->setStatus('created');
        $this->assertSame('created', $LoyaltyCard->getStatus());
    }

}
