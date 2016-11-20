<?php

namespace Silnin\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SilninOAuthBundle:Default:index.html.twig');
    }
}
