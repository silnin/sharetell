<?php

namespace Silnin\ShareTell\DashboardBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
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
     * @var StoryRepository
     */
    private $storyRepository;

    /**
     * @param EngineInterface $twigEngine
     * @param StoryRepository $storyRepository
     */
    public function __construct(EngineInterface $twigEngine, StoryRepository $storyRepository)
    {
        $this->twigEngine = $twigEngine;
        $this->storyRepository = $storyRepository;
    }

    public function indexAction()
    {
        $params = [];
//        $params['username'] = $this->userRepository->getByEmail('gft_bak@hotmail.com')->getUsername();
        $params['stories'] = $this->storyRepository->getPublicActiveStories();
        return $this->twigEngine->renderResponse('SilninShareTellDashboardBundle:Default:index.html.twig', $params);
    }
}
