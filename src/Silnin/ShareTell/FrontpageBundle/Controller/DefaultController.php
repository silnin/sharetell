<?php

namespace Silnin\ShareTell\FrontpageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SilninShareTellFrontpageBundle:Default:index.html.twig');
    }
}
