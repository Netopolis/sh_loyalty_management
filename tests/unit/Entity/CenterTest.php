<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Entity\User;
use Behat\Transliterator\Transliterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\TestCase;

class CenterTest extends TestCase
{

    public $center;
    private $name;
    private $phone;
    private $cardsEmitted;
    private $users;
    private $customers;
    private $slug;
    private $phone_format;
    private $image_center;
    private $published;


    public function testName()
    {
        $center = new Center();
        $center->setName('centre SensioLabs');
        $this->assertSame('centre SensioLabs', $center->getName());
    }

    public function testPhone()
    {
        $center = new Center();
        $phone = '03' . mt_rand(11111111,99999999);
        $center->setPhone($phone);
        $this->assertSame($phone, $center->getPhone());
    }

    public function testEmail()
    {
        $center = new Center();
        $email = 'club-test@shinigami.com';
        $center->setEmail($email);
        $this->assertSame($email, $center->getEmail());
    }

    public function testAddress()
    {
        $center = new Center();
        $address = '23 boulevard des pucelles';
        $center->setAddress($address);
        $this->assertSame($address, $center->getAddress());
    }

    public function testZipCode()
    {
        $center = new Center();
        $zipCode = '21000';
        $center->setZipCode($zipCode);
        $this->assertSame($zipCode, $center->getZipCode());
    }

    public function testCity()
    {
        $center = new Center();
        $city = 'Dijon';
        $center->setCity($city);
        $this->assertSame($city, $center->getCity());
    }

    public function testCountry()
    {
        $center = new Center();
        $country = 'France';
        $center->setCountry($country);
        $this->assertSame($country, $center->getCountry());
    }

    public function testCenterCode()
    {
        $center = new Center();
        $centerCode = 118;
        $center->setCenterCode($centerCode);
        $this->assertSame($centerCode, $center->getCenterCode());
    }

    public function testCardsEmitted()
    {
        $center = new Center();
        $loyalCard = new LoyaltyCard();
        $center->addCardsEmitted($loyalCard);
        $this->assertContains($loyalCard, $center->getCardsEmitted());
    }

    public function testUsers()
    {
        $center = new Center();
        $user = new User();
        $center->addUser($user);
        $this->assertContains($user, $center->getUsers());
    }

    public function testCustomers()
    {
        $center = new Center();
        $customer = new Customer();
        $center->addCustomer($customer);
        $this->assertContains($customer, $center->getCustomers());
    }

    public function testSlug()
    {
        $center = new Center();
        $center->setName('Sensiolabs Laser Game gotcha');
        $slug = Transliterator::transliterate($center->getName());
        $center->setSlug($slug);
        $this->assertSame($slug, $center->getSlug());
    }

    public function testPhone_Format()
    {
        $center = new Center();
        $phone = '03' . mt_rand(11111111,99999999);
        $center->setPhone($phone);
        $phone_format = wordwrap($phone, 2, " ", true);
        $this->assertSame($phone_format, $center->getPhone_Format());
    }

    public function testCenterImage()
    {
        $center = new Center();
        $img = 'shinigami-laser_5.jpg';
        $center->setCenterImage($img);
        $this->assertSame($img, $center->getCenterImage());
    }

    public function testIsPublished()
    {
        $center = new Center();
        $published = true;
        $center->setPublished($published);
        $this->assertTrue($published, $center->getPublished());
    }

}
