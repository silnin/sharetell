<?php

namespace Silnin\ShareTell\DashboardBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Silnin\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * /dashboard
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $me */
        $me = $this->tokenStorage->getToken()->getUser();

        $params = [];
        $params['me'] = $me;
        $params['public_stories'] = $this->storyRepository->getPublicUnjoinedStories($me);
        $params['joined_stories'] = $this->storyRepository->getJoinedStories($me);
        $params['created_stories'] = $this->storyRepository->getAllStoriesCreatedByUser($me);


//        return new JsonResponse(
//            [
//                'pub' => count($params['public_stories']),
//                'joi' => count($params['joined_stories']),
//                'cre' => count($params['created_stories'])
//            ],
//            200
//        );

        return $this->twigEngine->renderResponse('SilninShareTellDashboardBundle:Default:index.html.twig', $params);
    }
}
