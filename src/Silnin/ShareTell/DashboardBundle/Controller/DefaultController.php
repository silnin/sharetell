<?php

namespace Silnin\ShareTell\DashboardBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Silnin\UserBundle\Entity\User;
use Silnin\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultController extends Controller
{
    /**
     * @var EngineInterface
     */
    private $twigEngine;

    /**
     * @var StoryRepository
     */
    private $storyRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param EngineInterface $twigEngine
     * @param StoryRepository $storyRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EngineInterface $twigEngine,
        StoryRepository $storyRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twigEngine = $twigEngine;
        $this->storyRepository = $storyRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function indexAction()
    {
        /** @var User $me */
        $me = $this->tokenStorage->getToken()->getUser();

        $params = [];
        $params['public_stories'] = $this->storyRepository->getPublicUnjoinedStories($me);
        $params['joined_stories'] = $this->storyRepository->getJoinedStories($me);

        return $this->twigEngine->renderResponse('SilninShareTellDashboardBundle:Default:index.html.twig', $params);
    }
}
