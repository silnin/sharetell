<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    public function registerAction(Request $request)
    {
        $params = [];

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $password2 = $request->request->get('password');

        if (
            isset($email)
            && isset($password)
            && isset($password2)
            && $password == $password2
        ) {
            // validate entries

            // check user available

            // create user

            // login user

            // set cookie

            // redirect to confirmation
            return $this->render('UserBundle:Default:register-confirmed.html.twig');
        }


        $params['email'] = null;

        if (isset($email) ) {
            $params['email'] = $email;
        }

        return $this->render('UserBundle:Default:register.html.twig', $params);
    }

    public function loginAction(Request $request)
    {
        // verify both fields

        //look up in database (hsah pw etc)

        // create session/cookie

        // forward to control panel
        return $this->redirectToRoute('browser_homepage');
    }

    public function logoutAction(Request $request)
    {
        // destroy session
        // remove cookie or something

        // forward to control panel
        return $this->redirectToRoute('frontpage_homepage');
    }
}
