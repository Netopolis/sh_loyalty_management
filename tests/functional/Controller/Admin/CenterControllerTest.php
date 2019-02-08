<?php

namespace App\Tests\Functional\Controller\Admin;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\CenterType;
use App\Repository\CenterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/admin/centers")
 */
class CenterControllerTest extends WebTestCase
{
	private $routeRedirect = 'admin_login';

    /**
     * @Route("/", name="center_index", methods={"GET", "POST"})
     */
    public function testIndex()
    {
        /** Authenticate directly
         */
        $client = $this->createAuthorizedClient();

        /** Go to the page
         */
        $crawler = $client->request('GET', '/admin/centers/');

        /** Test everything's Ok
         */
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @Route("/new", name="center_new", methods={"GET","POST"})
     */
    public function testNewCenter()
    {
        /** Authenticate directly
         */
        $client = $this->createAuthorizedClient();

        /** Go to page
         */
        $crawler = $client->request('GET', '/admin/centers/new');

        /** Test that everything's Ok
         */
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testCenterUrlsWithAuthorized($url)
    {

        $client = $this->createAuthorizedClient();

        /** Test Urls
         */
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function urlProvider()
    {
        yield ['/admin/centers/1'];
        yield ['/admin/centers/edit/1'];
    }

    /**
     * @dataProvider unauthorizedUrlProvider
     */
    public function testCenterUrlsWithUnauthorized($url)
    {

        $client = self::createClient();

        /** Test Urls
         */
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function unauthorizedUrlProvider()
    {
        yield ['/admin/centers/'];
        yield ['/admin/centers/new'];
        yield ['/admin/centers/1'];
        yield ['/admin/centers/edit/1'];
    }


    /** Helper function for creating authorized clients, without filling the admin login form
     * @return Client
     */
    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'hugueswf3@gmail.com']);

        $firewallName = 'admin_area';
        $firewallContext = 'shared_context';

        $token = new UsernamePasswordToken($person, 'hugues', $firewallName, $person->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}