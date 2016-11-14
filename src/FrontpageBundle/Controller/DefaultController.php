<?php

namespace FrontpageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrontpageBundle:Default:index.html.twig');
    }
}
