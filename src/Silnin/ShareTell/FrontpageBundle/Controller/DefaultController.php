<?php

namespace Silnin\ShareTell\FrontpageBundle\Controller;

use Silnin\ShareTell\StoryBundle\Repository\ContributionRepository;
use Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository;
use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                'Location' => 'http://sharetell.silnin.nl/oauth/redirect'
            )
        );
    }

    public function assetLinksAction()
    {
        $headers = [];
        $headers['Content-Type'] = 'application/json';

        $content = [];
        $content['relation'] = ["delegate_permission/common.handle_all_urls"];
        $target = [];
        $target['namespace'] = 'android_app';
        $target['package_name'] = 'nl.silnin.sharetell';
        $target['sha256_cert_fingerprints'] = ["C1:71:06:7D:1D:B9:05:09:89:CC:D2:57:46:3C:C9:0B:B9:23:A5:A3:FE:E0:B6:C0:5C:80:CF:00:CC:3F:57:EC"];
        $content['target'] = $target;

        return new JsonResponse([$content], 200, $headers);
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
