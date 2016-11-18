<?php

namespace Silnin\ShareTell\StoryBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\ContributionRepository;
use Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository;
use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;
use Silnin\UserBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @var EngineInterface
     */
    private $twigEngine;

    /**
     * @var ParticipantRepository
     */
    private $participantRepository;
    /**
     * @var StoryRepository
     */
    private $storyRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @param EngineInterface $twigEngine
     * @param StoryRepository $storyRepository
     * @param ParticipantRepository $participantRepository
     * @param TokenStorageInterface $tokenStorage
     * @param ContributionRepository $contributionRepository
     */
    public function __construct(
        EngineInterface $twigEngine,
        StoryRepository $storyRepository,
        ParticipantRepository $participantRepository,
        TokenStorageInterface $tokenStorage,
        ContributionRepository $contributionRepository
    ) {
        $this->twigEngine = $twigEngine;
        $this->participantRepository = $participantRepository;
        $this->storyRepository = $storyRepository;
        $this->tokenStorage = $tokenStorage;
        $this->contributionRepository = $contributionRepository;
    }

    public function indexAction()
    {
        return $this->render('SilninShareTellStoryBundle:Default:index.html.twig');
    }

    public function joinAction($reference, Request $request)
    {
        // get user
        $user = $this->tokenStorage->getToken()->getUser();

        // get story from repo
        $story = $this->storyRepository->getStoryByReference($reference);

        // create participant
        $this->participantRepository->createParticipant($user, $story);

        // redirect to play
        return $this->redirect('/story/' . $reference);
    }

    public function playAction($reference)
    {
        $params = [];

        // get story from repo
        $story = $this->storyRepository->getStoryByReference($reference);

        // get contributions from repo
        $contributions = $this->contributionRepository->getContributionsForStory($story);

        // get participants from repo
        $participants = $this->participantRepository->getParticipantsForStory($story);

        $params['story'] = $story;
        $params['contributions'] = $contributions;
        $params['participants'] = $participants;

        return $this->twigEngine->renderResponse('SilninShareTellStoryBundle:Default:play.html.twig', $params);
    }
}
