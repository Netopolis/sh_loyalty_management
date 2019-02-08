<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Center;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public $User;
    private $firstName;
    private $lastName;
    private $email;
    private $password;
    private $isActive;
    private $roles = [];
    private $center;

    public function testFirstName()
    {
        $User = new User();
        $User->setFirstName('Prenom');
        $this->assertSame('Prenom', $User->getFirstName());
    }

    public function testLastName()
    {
        $User = new User();
        $User->setLastName('Nom');
        $this->assertSame('Nom', $User->getLastName());
    }

    public function testEmail()
    {
        $User = new User();
        $User->setEmail('ab@gmail.com');
        $this->assertSame('ab@gmail.com', $User->getEmail());
    }

    public function testPassword()
    {
        $User = new User();
        $User->setPassword('678UUIO');
        $this->assertSame('678UUIO', $User->getPassword());
    }

    public function testIsActive()
    {
        $User = new User();
        $User->setIsActive(true);
        $this->assertSame(true, $User->getIsActive());
    }

    public function testRoles()
    {
        $User = new User();
		$roles = array("Role 1");
        $User->setRoles($roles);
        $this->assertSame($roles, $User->getRoles());
    }

    public function testCenter()
    {
        $User = new User();
		$center = new center();
        $User->setCenter($center);
        $this->assertSame($center, $User->getCenter());
    }

}
