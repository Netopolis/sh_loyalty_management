<?php

namespace App\Controller\Sec;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\SessionLogoutHandler;


class AdminSecurityControllerTest extends WebTestCase
{

    /**
     * Login for the admin section
     * @Route("/admin/login", name="admin_login", methods={"GET","POST"})
     */
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        /** redirected to admin login
         */
        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();

            $this->assertTrue($client->getResponse()->isSuccessful());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertContains('/admin/login', $crawler->getUri());

            $form = $crawler->selectButton('Connexion')->form();
            $form['_email'] = 'hugueswf3@gmail.com';
            $form['_password'] = 'hugues';
            $crawler = $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect());
            $crawler = $client->followRedirect();
        }

        /** We should be on the admin page
         */
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin', $crawler->getUri());
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function testLogout()
    {

        $client = self::createClient();
        $session = new Session(new MockFileSessionStorage());

        $crawler = $client->request('GET', '/admin/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin', $crawler->getUri());

    }

}
