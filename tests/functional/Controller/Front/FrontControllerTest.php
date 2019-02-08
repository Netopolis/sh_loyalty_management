<?php
/**
 * Created by Cecile
 */

namespace App\Tests\functional\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerTest extends WebTestCase
{

    public function testIndex()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        // $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());


    }

    public function testDisplayJeu()
    {

        $client = static::createClient();

        //$client = new Client();

        $crawler = $client->request('GET', '/le-jeu');

        // $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testShowGamePoints()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/les-points');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testDisplayCenters()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/shinigamilaserclubs');

        // $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    /**
     * @dataProvider urlProvider
     */
    public function testFrontUrls($url)
    {
        $client = self::createClient();

        /** Test a few extra Urls
         */
        $client->request('GET', $url);

        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();
        }

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/inscription'];
        yield ['/login/'];
        yield ['/logout']; // it just travels through - check for a redirect
    }

}