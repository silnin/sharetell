<?php

namespace Silnin\ShareTell\DashboardBundle\Controller;

use Silnin\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class DefaultController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EngineInterface
     */
    private $twigEngine;

    /**
     * @param UserRepository $userRepository
     * @param EngineInterface $twigEngine
     */
    public function __construct(UserRepository $userRepository, EngineInterface $twigEngine)
    {
        $this->userRepository = $userRepository;
        $this->twigEngine = $twigEngine;
    }

    public function indexAction()
    {
        $params = [];
        $params['username'] = $this->userRepository->getByEmail('gft_bak@hotmail.com')->getUsername();

        return $this->twigEngine->renderResponse('SilninShareTellDashboardBundle:Default:index.html.twig', $params);
    }
}
