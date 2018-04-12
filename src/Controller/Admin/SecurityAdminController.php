<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityAdminController extends Controller
{
    /**
     * @Route("/login", name="admin_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the security error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

}
