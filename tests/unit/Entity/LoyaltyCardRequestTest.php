<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use App\Entity\LoyaltyCardRequest;
use PHPUnit\Framework\TestCase;

class LoyaltyCardRequestTest extends TestCase
{

    public $LoyaltyCardRequest;
    private $customer;
    private $dateOfRequest;
    private $status;

    public function testCustomer()
    {
        $LoyaltyCardRequest = new LoyaltyCardRequest();
		$customer = new Customer();
        $LoyaltyCardRequest->setCustomer($customer);
        $this->assertSame($customer, $LoyaltyCardRequest->getCustomer());		
    }

    public function testDateOfRequest()
    {
        $LoyaltyCardRequest = new LoyaltyCardRequest();
		$date = new \DateTime('2009-03-01');
		$date->format('Y-m-d H:i:s');
        $LoyaltyCardRequest->setDateOfRequest($date);
        $this->assertSame($date, $LoyaltyCardRequest->getDateOfRequest());
    }

    public function getStatus()
    {
        $LoyaltyCardRequest = new LoyaltyCardRequest();
        $LoyaltyCardRequest->setStatus(2);
        $this->assertSame(2, $LoyaltyCardRequest->getStatus());
    }

}
