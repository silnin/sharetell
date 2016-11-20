<?php

namespace Silnin\ShareTell\StoryBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use Silnin\ShareTell\StoryBundle\Entity\Story;
use Silnin\ShareTell\StoryBundle\Repository\ContributionRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;

class StoryController extends FOSRestController
{
    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @param StoryRepository $storyRepository
     * @param Container $container
     * @param ContributionRepository $contributionRepository
     */
    public function __construct(
        StoryRepository $storyRepository,
        Container $container,
        ContributionRepository $contributionRepository
    ) {
        $this->storyRepository = $storyRepository;
        $this->container = $container;
        $this->contributionRepository = $contributionRepository;
    }

    public function getAction(Request $request)
    {
        $me = $this->container->get('security.token_storage')->getToken()->getUser();

        $scope = 'public';
        if ($request->query->get('scope')) {
            $scope = $request->query->get('scope');
        }

        $status = 'active';
        if ($request->query->get('status')) {
            $status = $request->query->get('status');
        }

        $stories = $this->storyRepository->getStories($scope, $me, $status);

        $view = $this->view($stories, 200)->setFormat('json');

        return $this->handleView($view);
    }

    public function getSingleAction($reference)
    {
        $story = $this->storyRepository->getStoryByReference($reference);

        if (!$story) {
            $view = $this->view(null, 404)->setFormat('json');
            return $this->handleView($view);
        }

        $view = $this->view($story, 200)->setFormat('json');
        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $payload = $body['payload'];

        //@todo validation
        //@todo is the current user allowed to do this?
        $me = $this->container->get('security.token_storage')->getToken()->getUser();

        try {
            $story = $this->storyRepository->createStoryFromPayload($payload, $me);
            $view = $this->view($story, 200)->setFormat('json');
            return $this->handleView($view);

        } catch (EntityNotFoundException $e) {
            $view = $this->view($this->exceptionToViewable($e), 400)->setFormat('json');
            return $this->handleView($view);
        } catch (Exception $e) {
            $view = $this->view($this->exceptionToViewable($e), 500)->setFormat('json');
            return $this->handleView($view);
        }
    }

    private function exceptionToViewable(Exception $e)
    {
        $error = [];
        $error['code'] = $e->getCode();
        $error['message'] = $e->getMessage();
        $error['type'] = get_class($e);
        $error['trace'] = $e->getTraceAsString();

        return $error;
    }

    public function joinAction($reference)
    {
        //@todo validation
        //@todo is the current user allowed to do this?
        $me = $this->container->get('security.token_storage')->getToken()->getUser();

        try {
            $story = $this->storyRepository->getStoryByReference($reference);
            $this->storyRepository->joinStory($story, $me);

            $view = $this->view($story, 200)->setFormat('json');
            return $this->handleView($view);

        } catch (EntityNotFoundException $e) {
            $view = $this->view($this->exceptionToViewable($e), 400)->setFormat('json');
            return $this->handleView($view);
        } catch (Exception $e) {
            $view = $this->view($this->exceptionToViewable($e), 500)->setFormat('json');
            return $this->handleView($view);
        }
    }

    public function contributeAction($reference, Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $payload = $body['payload'];

        //@todo validation
        //@todo is the current user allowed to do this?
        $me = $this->container->get('security.token_storage')->getToken()->getUser();

        try {
            $story = $this->storyRepository->getStoryByReference($reference);

            if ($story->getStatus() != Story::STATUS_ACTIVE) {
                $result = [];
                $result['error'] = "This story isn't active.";

                $view = $this->view($result, 403)->setFormat('json');
                return $this->handleView($view);
            }

            if (!$this->storyRepository->isItMyTurn($story, $me)) {
                $result = [];
                $result['error'] = "You'll have to wait your turn.";

                $view = $this->view($result, 403)->setFormat('json');
                return $this->handleView($view);
            }

            $content = $payload['content'];
            $this->contributionRepository->createContribution($story, $me, $content);

            $view = $this->view($story, 200)->setFormat('json');
            return $this->handleView($view);

        } catch (EntityNotFoundException $e) {
            $view = $this->view($this->exceptionToViewable($e), 400)->setFormat('json');
            return $this->handleView($view);
        } catch (Exception $e) {
            $view = $this->view($this->exceptionToViewable($e), 500)->setFormat('json');
            return $this->handleView($view);
        }
    }
}
