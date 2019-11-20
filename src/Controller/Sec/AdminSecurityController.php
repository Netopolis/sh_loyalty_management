<?php

namespace App\Controller\Sec;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{

    /**
     * Login for the admin section of the website
     * @Route("/admin/login", name="admin_login", methods={"GET","POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // redirect already logged in customers who might stumble on this route
        // Removed but should follow the same rules as below with ROLE_USER and redirect to 'home' - No, ROLE_USER is inherited
		// if ($this->getUser() instanceOf Customer) {
		//	return and redirect to public part of the website } 

        if($this->isGranted('ROLE_STAFF')) {
            return $this->redirectToRoute('admin_home_dispatch');
        }
		
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse
     */
    public function logout(AuthenticationUtils $authenticationUtils)
    {
		return $this->redirectToRoute('admin_login');
    }

}
