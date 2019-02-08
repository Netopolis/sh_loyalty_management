<?php

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\AdminController;
use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Entity\LoyaltyCardRequest;
use App\Entity\User;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use App\Service\AdminUserService;
use App\Service\CardSchemeEncoder;
use App\Service\QRCodeEncoder;
use App\Service\YamlStatsProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{

    /**
     * @Route("/admin/dashboard", name="admin_board", methods={"GET","POST"})
     */
    public function testIndex()
    {
        $client = static::createClient();

        /** Go to the /admin area
         */
        $crawler = $client->request('GET', '/admin');

        /** test if we are redirected, which means we are not logged in or don't have ROLE_STAFF
         */
        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();

            $this->assertTrue($client->getResponse()->isSuccessful());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertContains('/admin/login', $crawler->getUri());

            /** Next we must log in, as admin or staff, see AdminSecurityController and admin_login route
             */

            $form = $crawler->selectButton('Connexion')->form();
            $form['_email'] = 'cboucajay@gmail.com';
            $form['_password'] = 'cecile';
            $crawler = $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect());
            $crawler = $client->followRedirect();
        }

        /** Check that we arrived on the admin index page
         */
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin', $crawler->getUri());
        $this->assertSame(' Tableau de bord ', $crawler->filter('h2')->text());
        $this->assertSame(' Tableau de bord ', $crawler->filter('h2')->text());

    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminUrls($url)
    {
        $client = self::createClient();

        /** Go to the /admin area
         */
        $crawler = $client->request('GET', '/admin');

        /** Login
         */
        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            /** Handle form
             */

            $form = $crawler->selectButton('Connexion')->form();
            $form['_email'] = 'cboucajay@gmail.com';
            $form['_password'] = 'cecile';
            $crawler = $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect());
            $crawler = $client->followRedirect();
        }

        /** Test most Urls
         */
        $client->request('GET', $url);

        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();
        }

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
		yield ['/admin/dashboard/'];
        yield ['/admin/index_old'];
/*        yield ['/admin/cardvalidate/1'];
        yield ['/admin/cardinformcustomer/1'];
        yield ['/admin/cardwithdraw/1'];
        yield ['/admin/cardrefuse/2']; // No, these are just redirects */
        yield ['/admin/search'];
        yield ['/admin/centers/'];
        yield ['/admin/centers/new'];
		yield ['/admin/centers/1'];
		yield ['/admin/centers/edit/1'];
        yield ['/admin/customers/'];
        yield ['/admin/customers/activity'];
        yield ['/admin/customers/inactive'];
        yield ['/admin/customers/new'];
		yield ['/admin/customers/1'];
		yield ['/admin/customers/edit/1'];
		yield ['admin/customers/activity/edit/1'];
        yield ['/admin/customers/disabled/1'];
        yield ['/admin/customers/enabled/1'];
        yield ['/admin/loyalty/card/'];
        yield ['/admin/loyalty/card/inactive'];
        yield ['/admin/loyalty/card/new'];
		yield ['/admin/loyalty/card/1'];
		yield ['/admin/loyalty/card/edit/1'];
        yield ['/admin/users/'];
        yield ['/admin/users/by-center'];
        yield ['/admin/users/inactive'];
        yield ['/admin/users/new'];
		yield ['/admin/users/1'];
		yield ['/admin/users/edit/1'];
    }

    /**
     * Test the Search bar in the left menu
     */
    public function testSearchBar()
    {
        $client = static::createClient();

        /** Go to the /admin area
         */
        $crawler = $client->request('GET', '/admin');

        /** If necessary, log in
         */
        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            /** Handle form
             */

            $form = $crawler->selectButton('Connexion')->form();
            $form['_email'] = 'cboucajay@gmail.com';
            $form['_password'] = 'cecile';
            $crawler = $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect());
            $crawler = $client->followRedirect();
        }

        /** Select the searchBar
         */
        $searchForm = $crawler->filter('#search > form')->form();
        // will return some Loyalty Cards, or some customer codes, for center '111'
        $value = ['form[search]' => '11110001'];
        $crawler = $client->submit($searchForm, $value);

        /** Check the search results page
         */
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testDashBoard()
    {

        /** From now on, we'll authenticate directly
         */
        $client = $this->createAuthorizedClient();

        /** Go to the admin area - the dashboard is the default view
         */
        $crawler = $client->request('GET', '/admin');

        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();
        }

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('/admin/dashboard', $crawler->getUri());
        $this->assertSame(' Tableau de bord ', $crawler->filter('h2')->text());

    }

    public function testLoyaltyManager()
    {

        /** Authenticate directly
         */
        $client = $this->createAuthorizedClient();

        /** Go to the loyalty manager page
         */
        $crawler = $client->request('GET', '/admin/loyalty/manager');

        /** And we just need to test that everything's Ok
         */
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

    }


    public function testRefuseCard()
    {

        $controller = new AdminController();
        $loyaltyCardRequest = new LoyaltyCardRequest();

        $assign = $controller->RefuseCard($loyaltyCardRequest);
        $check = "Controller Card Refused est testé.";
        $this->assertEquals($check, $assign);

    }

    public function testInformCustomerCard()
    {

        $controller = new AdminController();
        $LoyaltyCard = new LoyaltyCard();
        $workflows = new Registry();

        $assign = $controller->InformCustomerCard($LoyaltyCard, $workflows);
        $check = "Controller Card Infos est testé.";
        $this->assertEquals($check, $assign);

    }

    public function testCustomerCardWithDraw()
    {

        $controller = new AdminController();
        $LoyaltyCard = new LoyaltyCard();
        $workflows = new Registry();

        $assign = $controller->InformCustomerCard($LoyaltyCard, $workflows);
        $check = "Controller Card Infos est testé.";
        $this->assertEquals($check, $assign);

    }


    /** Helper function for creating authorized clients, without filling the admin login form
     * @return Client
     */
    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'cboucajay@gmail.com']);

        $firewallName = 'admin_area';
        $firewallContext = 'shared_context';

        $token = new UsernamePasswordToken($person, 'cecile', $firewallName, $person->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
