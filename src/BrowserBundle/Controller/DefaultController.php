<?php

namespace BrowserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $params = [];
        $params['name'] = 'Somgai';
        return $this->render('BrowserBundle:Default:index.html.twig', $params);
    }
}
