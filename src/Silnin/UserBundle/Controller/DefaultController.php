<?php

namespace Silnin\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SilninUserBundle:Default:index.html.twig');
    }
}
