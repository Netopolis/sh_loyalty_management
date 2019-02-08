<?php
/**
 * Created by Hugues
 * 08/01/2019
 */

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AdminUserService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Checks permissions over the whole admin area
 * First checks if a user has ROLE_STAFF.
 * If he / she is logged in AND has ROLE_STAFF --> get first name, last name, and the center / club where he works.
 * Used for displaying user information in the admin area, and the tasks he / she has to undertake.
 */
class AdminUserServiceTest extends TestCase
{

    public function testGetLegitimateUser() {

        // testing info for a user to access the admin area with ROLE_STAFF

        $user = new User();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->any())
            ->method('find')
            ->willReturn($user);

        $objManager = $this->createMock(ObjectManager::class);
        $objManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($userRepository);

        if (is_null($user->getCenter())) {
            $center = 'Admin';
        } else {
            $center = $user->getCenter()->getName();
        }

        $admServ = new AdminUserService();

        $userData = $admServ->getLegitimateUser($user);

        $this->assertArrayHasKey('firstName', $userData);
        $this->assertArrayHasKey('lastName', $userData);
        $this->assertArrayHasKey('center', $userData);
        $this->assertCount(3, $userData);

    }

}