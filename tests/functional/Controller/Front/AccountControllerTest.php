<?php
/**
 * Created by Hugues
 */

namespace App\Tests\functional\Controller\Front;

use App\Entity\Customer;
use App\Entity\CustomerActivity;
use App\Entity\LoyaltyCardRequest;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\FrontCustomerType;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use App\Service\FrontMemberService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Factory\QrCodeFactory;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;


/**
 * @Route("/")
 */
class AccountControllerTest extends WebTestCase
{

    public function testGetMember(){

        /** Create admin client
         */
        $client = $this->createAdminClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $cookie = $client->getCookieJar()->get($session); // cookie has same name as the session

        /** Then to get the the user name or Id, you can call
         */
        // $session->getName() OR $session->getId();
        /** But we don't have the ROLE yet, for that you can call
         * The UserNamePasswordToken with the reverse of
         * $session->set('_security_' . $firewallContext, serialize($token));
         */

    }

    /**
     * @Route("/account/", name="account", methods={"GET", "POST"})
     */
    public function testAccountIndex()
    {
        /** Start with anonymous client, to test redirect to login page
         */
        $anonymous = static::createClient();
        $crawler = $anonymous->request('GET', '/login/');
        if ($anonymous->getResponse()->isRedirect()) {
            $crawler = $anonymous->followRedirect();

            $this->assertEquals(200, $anonymous->getResponse()->getStatusCode());
            $this->assertContains('/login', $crawler->getUri());
        }

        /** Authenticate directly, without filling the login form
         */
        $client = $this->createCustomerClient();
        if ($client->getResponse()->isRedirect()) {
            $crawler = $client->followRedirect();
        }

        $this->assertSame('Bienvenue', $crawler->filter('h4')->text());

        /** Go to the account default page
         */
        $crawler = $client->request('GET', '/account/member/scores');

        /** Test everything is Ok
         */
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('/account/member/scores', $crawler->getUri());

    }

    /**
     * Scores route to customer account
     * @Route("/account/member/{part}", name="account_member")
     * @param Request $request
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function accountScores($part, Request $request, CenterRepository $centerRepository, QrCodeFactory $qrCodeFactory, ObjectManager $manager, FrontMemberService $FrontMemberService): Response {

        $customerId = $this->getUser()->getId();

        // If the customer is not logged, redirects him straight to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($customerId);

        $generalInfo = $FrontMemberService->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        return $this->render('front/account/account_index.html.twig', [
            'centers' => $generalInfo['centers'],
            'customer' => $customer,
            'customer_image' => $customer->getImageProfile(),
            'customer_activity' => $generalInfo['customerActivity'],
            'loyalty_cards' => $generalInfo['loyalty_cards'],
            'card_request' => $generalInfo['customerCardRequest'],
            'qr_code' => $generalInfo['qr_code'],
            'member_menu' => $part
        ]);

    }

    /**
     * Requests for loyalty cards, sent by customers
     * @Route("/account/cardrequest/", name="card_request", methods={"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function cardRequest(Request $request, CenterRepository $centerRepository): Response {

        // If the customer is not logged, redirects him straight to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // $paramId = $customer->getId(); // get customer passed as param
        $customerId = $this->getUser()->getId(); // get customer in session
        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($customerId);

        // compare, check if the customer in session is the right person, and if he doesn't already have a card
        if (count($customer->getCards()) > 0) {
            return $this->redirectToRoute('home');
        }

        $loyaltyCardRequest = New LoyaltyCardRequest();
        $loyaltyCardRequest->setCustomer($customer);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loyaltyCardRequest);
        $entityManager->flush();

        // redirecting to the account page
        return $this->redirectToRoute("account_member", ["part" => "avantages"]);
    }

    /**
     * Needs customer.id, to know if the card request was sent, then needs to know the status of the loyalty card (or the action)
     * We will switch the views, or content in the wiews depending on the situation
     */
    public function cardDisplay() {
        return $this->render('front/components/_card_display.html.twig'/*, [
                'dataNeeded' => $dataNeeded
            ]*/);
    }

    /**
     * @Route("/account/infos-edit/", name="member_edit", methods={"GET", "POST"})
     */
    public function memberEdit(Request $request, CenterRepository $centerRepository, Packages $packages, QrCodeFactory $qrCodeFactory, FrontMemberService $FrontMemberService, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder){
		
		$img_profile_name = "profil_default_image.png";

        // If the customer is not logged, redirects him straight to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $customerId = $this->getUser()->getId();
        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($customerId);


        if(!empty($customer->getImageProfile())){
			
			$img_profile_name = $customer->getImageProfile();
			
            # On passe à notre formulaire l'URL de la featuredImage
            $options = [
                'image_url' => $packages->getUrl('images/members/profile_images/'
                    . $customer->getImageProfile())
            ];

            # Récupération de l'image
            $featuredImageName = $customer->getImageProfile();

            # Notre formulaire attend une instance de File pour l'edition
            # de la featuredImage
            $customer->setImageProfile(
                new File($this->getParameter('members_profile_images_dir')
                    . '/' . $featuredImageName)
            );
        }

        $form = $this->createForm(FrontCustomerType::class, $customer, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImage */
            $featuredImage = $customer->getImageProfile();


            if($featuredImage != null){
                $fileName =  uniqid("imgp_") . "_". $customer->getId()
                    . '.' . $featuredImage->guessExtension();

                try {
                    $featuredImage->move(
                        $this->getParameter('members_profile_images_dir'),
                        $fileName
                    );
                } catch (FileException $e) {

                }
            }else{
                $fileName =  $img_profile_name;
            }

            # Mise à jour de l'image
            $customer->setImageProfile($fileName);

            $customer->setPassword($passwordEncoder
                ->encodePassword($customer, $customer->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('notice',
                'Félicitation, votre profil a été mis à jour !');				
				
			return $this->redirectToRoute("member_edit");				

        }

        $generalInfo = $FrontMemberService->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        return $this->render('front/account/account_edit.html.twig', [
            'centers' => $generalInfo['centers'],
            'customer' => $customer,
			'customer_image' => $img_profile_name,
            'customer_activity' => $generalInfo['customerActivity'],
            'loyalty_cards' => $generalInfo['loyalty_cards'],
            'card_request' => $generalInfo['customerCardRequest'],
            'qr_code' => $generalInfo['qr_code'],
            'member_menu' => "profile",
            'form' => $form->createView()
        ]);
    }

    /** Helper function for creating customer clients, without filling the login form
     * @return Client
     */
    protected function createCustomerClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $rand = mt_rand(1, 30); // random customer
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(Customer::class)->findOneBy(['email' => 'client' . $rand . '@gmail.com']);

        $firewallName = 'main';
        $firewallContext = 'shared_context';

        $token = new UsernamePasswordToken($person, 'client' . $rand, $firewallName, $person->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    /** Helper function for creating admin clients, without filling the login form
     * @return Client
     */
    protected function createAdminClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'hugueswf3@gmail.com']);

        $firewallName = 'main';
        $firewallContext = 'shared_context';

        $token = new UsernamePasswordToken($person, 'hugues', $firewallName, $person->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

}