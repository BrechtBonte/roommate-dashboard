<?php

namespace RoommateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthenticationController extends Controller
{
    public function loginAction()
    {
        /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render(
            'RoommateBundle:Authentication:login.html.twig',
            array(
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError(),
            )
        );
    }

    public function loginCheckAction()
    {
        // This controller will not be executed
    }
}
