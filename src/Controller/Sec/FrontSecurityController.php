<?php

namespace App\Controller\Sec;

use App\Entity\Customer;
use App\Form\FrontCustomerType;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class FrontSecurityController extends AbstractController
{

    /**
     * @Route("/login/", name="login", methods={"GET","POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @param CenterRepository $centerRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils, CenterRepository $centerRepository): Response
    {
        // redirect already logged in users
        if($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /*$customer = new Customer();
        $form = $this->createForm(FrontCustomerType::class, $customer);*/

        return $this->render('front/login/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'centers' => $centerRepository->findBy([], ['name' => 'ASC'])
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/inscription", name="inscription_membre")
     * @param Customer|null $customer
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param CenterRepository $centerRepository
     * @param Packages $packages
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function formInsc(Customer $customer = null,
                              Request $request,
                              CustomerRepository $customerRepository,
                              UserPasswordEncoderInterface $passwordEncoder,
                              CenterRepository $centerRepository,
                              Packages $packages)
    {

        // Get the last customer code
        $lastCustomerCode = $customerRepository->getMaxCustomerCode();
        // Increment it by 1 to set the new code
        $customerCode = (int)$lastCustomerCode['lastCode'] +1;


        $customer = new Customer();
        $customer->setCustomerCode($customerCode);
		
		$img_profile_name= $customer->getImageProfile();
		
		$options = [
                'image_url' => $packages->getUrl('images/members/profile_images/' . $img_profile_name),
				'image_name' => $img_profile_name
            ];
		
		$customer->setImageProfile(
			new File($this->getParameter('members_profile_images_dir')
				. '/' . $img_profile_name)
		);

		
        $form = $this->createForm(FrontCustomerType::class, $customer, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer->setPassword($passwordEncoder
                ->encodePassword($customer, $customer->getPassword()));
			
			/** @var UploadedFile $featuredImage */
			$old_image = $form->getConfig()->getOptions()['image_name'];
			$customer->setImageProfile($old_image);
			// $customer->setImageProfile($this->img_profile_name);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

            return $this->redirectToRoute('login');
        }

        return $this->render('front/account/form_insc.html.twig', [
            'customer' => $customer,
            'centers' => $centerRepository->findBy([], ['name' => 'ASC']),
            'form' => $form->createView(),
        ]);
    }

}
