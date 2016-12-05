<?php

namespace Silnin\ShareTell\FrontpageBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\ContributionRepository;
use Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository;
use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @var ContributionRepository
     */
    private $contributionRepository;
    /**
     * @var ParticipantRepository
     */
    private $participantRepository;

    /**
     * @param EngineInterface $twigEngine
     * @param StoryRepository $storyRepository
     * @param ContributionRepository $contributionRepository
     * @param ParticipantRepository $participantRepository
     */
    public function __construct(
        EngineInterface $twigEngine,
        StoryRepository $storyRepository,
        ContributionRepository $contributionRepository,
        ParticipantRepository $participantRepository
    ) {
        $this->twigEngine = $twigEngine;
        $this->storyRepository = $storyRepository;
        $this->contributionRepository = $contributionRepository;
        $this->participantRepository = $participantRepository;
    }

    public function indexAction()
    {
        return $this->twigEngine->renderResponse('SilninShareTellFrontpageBundle:Default:index.html.twig', []);
    }

    public function testAction($returnurl)
    {
        return new Response(
            '',
            302,
            array(
                'Location' => $returnurl
            )
        );
    }

    public function readAction($reference)
    {
        $params = [];
        $params['story'] = $this->storyRepository->getStoryByReference($reference);
        $params['contributions'] = $this->contributionRepository->getContributionsForStory($params['story']);
        $params['participants'] = $this->participantRepository->getParticipantsForStory($params['story']);

        return $this->twigEngine->renderResponse('SilninShareTellFrontpageBundle:Default:read.html.twig', $params);
    }
}
