<?php

namespace Silnin\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage(); // WARNING! Symfony source code identifies this line as a potential security threat.
        }

        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        $params = [];
        $params['action'] = $this->get('router')->generate('silnin_o_auth_auth_login_check');
        $params['last_username'] = $lastUsername;

        if ($error) {
            $params['error'] = $error;
        }

        return $this->render(
            'SilninOAuthBundle:Security:login.html.twig',
            $params
        );
    }

    public function loginCheckAction(Request $request)
    {

    }
}