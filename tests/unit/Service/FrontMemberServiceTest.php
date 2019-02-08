<?php
/**
 * Created by Hugues
 * 08/01/2019
 */

namespace App\Tests\Unit\Service;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCardRequest;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use App\Service\FrontMemberService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Factory\QrCodeFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 */
class FrontMemberServiceTest extends KernelTestCase
{

    public function testGetInfoMember() {

        // testing if the service rightfully provides complete information about a customer accessing the front, private area : e.g., account

        self::bootKernel();

        $container = self::$kernel->getContainer();
        $container = self::$container;


        $customer = self::$container->get('doctrine')->getRepository(Customer::class)->findOneBy(array('email' => 'client1@gmail.com'));


        $customer = new Customer();
        //$customer->setPreferredCenter(1);

        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->expects($this->any())
            ->method('find')
            ->willReturn($customer);

        $qrCodeFactory = new QrCodeFactory();
        $centerRepository = self::$container->get('doctrine')->getRepository(Center::class);


        $manager = $this->createMock(ObjectManager::class);
        $manager->expects($this->any())
            ->method('getRepository')
            ->willReturn($customerRepository);

        $frontServ= new FrontMemberService();
        $generalInfo = $frontServ->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        $this->assertNotEmpty($generalInfo);
        $this->assertNotNull($generalInfo['customerActivity']);


    }


}