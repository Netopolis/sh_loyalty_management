<?php

namespace App\Tests\Unit\DataFixtures;

use App\DataFixtures\CenterFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class UserFixturesTest extends TestCase {

    public $manager;

    public function testLoad()
    {
		/*
        $fixture = new UserFixtures();
		$manager = new ObjectManager();

        $assign = $fixture->load($manager);
        // $check = "UserFixture Load() est testé.";
        // $this->assertEquals($check, $assign);
		
		$this->assertTrue($assign);
		*/
        
    }

}
