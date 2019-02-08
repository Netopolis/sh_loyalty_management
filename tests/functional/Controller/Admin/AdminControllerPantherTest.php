<?php
/**
 * Created by Hugues
 * Date: 22/01/2019
 */

namespace App\Tests\functional\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Panther\DomCrawler\Form As PantherForm;
use Symfony\Component\Panther\PantherTestCase;

class AdminControllerPantherTest extends PantherTestCase
{

    /**
     * @Route("/admin/dashboard", name="admin_board", methods={"GET","POST"})
     */
    public function testIndex()
    {
        $client = static::createPantherClient();

        /** Go to the /admin area
         */
        $crawler = $client->request('GET', 'http://localhost:8000/admin');

        /** test if we are redirected, which means we are not logged in or don't have ROLE_STAFF
         */
        if ($client->isFollowingRedirects()) {
            //$client->waitFor('.btn-admin-login', 30, 250);

            $this->assertContains('/admin', $client->getCurrentURL());
            sleep(2);

            /** Next we must log in, as admin or staff, see AdminSecurityController and admin_login route
             */

            $form = $crawler->selectButton('Connexion')->form();
            $values = ['_email' => 'cboucajay@gmail.com', '_password' => 'cecile'];
            $crawler = $client->submit($form, $values);

        }

        /** Check that we arrived on the admin index page
         */
        $this->assertEquals('Tableau de bord', $client->getTitle());
        $this->assertContains('/admin', $client->getCurrentURL());
        $this->assertSame(' Tableau de bord ', $crawler->filter('h2')->text());
        $this->assertSame(' Tableau de bord ', $crawler->filter('h2')->text());

    }

}