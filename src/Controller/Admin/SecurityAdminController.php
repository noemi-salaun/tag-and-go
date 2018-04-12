<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Security controller for admin pages.
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class SecurityAdminController extends Controller
{
    /**
     * Handle the login form.
     *
     * @Route("/login", name="admin_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // Get the security error if there is one.
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

}
