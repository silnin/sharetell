<?php

namespace Silnin\ShareTell\StoryBundle\Controller;

use InvalidArgumentException;
use Proxies\__CG__\Silnin\ShareTell\StoryBundle\Entity\Participant;
use Silnin\ShareTell\StoryBundle\Entity\Contribution;
use Silnin\ShareTell\StoryBundle\Entity\Story;
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

    public function newAction()
    {
    }

    public function playAction($reference)
    {
        $params = [];

        // get story from url reference key
        $story = $this->storyRepository->getStoryByReference($reference);

        // get contributions from repo
        $contributions = $this->contributionRepository->getContributionsForStory($story);

        // get participants from repo
        $participants = $this->participantRepository->getParticipantsForStory($story);

        $params['me']               = $this->tokenStorage->getToken()->getUser();
        $params['turn']             = $this->determineTurn($participants, $contributions);
        $params['story']            = $story;
        $params['contributions']    = $contributions;
        $params['participants']     = $participants;

        return $this->twigEngine->renderResponse('SilninShareTellStoryBundle:Default:play.html.twig', $params);
    }

    private function determineTurn(
        array $participants,
        array $contributions
    ) {

        if (count($participants) < 1) {
            throw new InvalidArgumentException('No participants defined for this game');
        }

        $lastContributorPosition = $this->getLastContributor($participants, $contributions);

        if (!is_null($lastContributorPosition)
            && array_key_exists($lastContributorPosition+1, $participants)
        ) {
            //@todo consider status of participant
            return $participants[$lastContributorPosition+1]->getUser();
        }

        //@todo consider status of participant
        return $participants[0]->getUser();
    }

    private function getLastContributor(array $participants, array $contributions)
    {
        $nextContributor = null;

        // determine author of last contribution

        /** @var Contribution $lastPost */
        $lastPost = end($contributions);
        $lastAuthor = $lastPost->getAuthor();

        /** @var Participant $participant */
        foreach ($participants as $key => $participant) {

            $lastContributorPosition = null;
            if ($lastAuthor->getId() == $participant->getUser()->getId()) {
                // this is the last contributor, get the next guy in line
                return $key;
            }
        }

        return null;
    }

    public function contributeAction($reference, Request $request)
    {
        /** @var Story $story */
        $story = $this->storyRepository->getStoryByReference($reference);

        /** @var User $me */
        $me = $this->tokenStorage->getToken()->getUser();

        $content = $request->get('contribution');
        $contribution = $this->contributionRepository->createContribution($story, $me, $content);

        if (!$contribution->getId()) {
            // failed
            return new Response(500, 'So sorry! We could not process your contribution');
        }

        // return back to play
        return $this->redirect('/story/' . $reference);
    }

    public function createAction()
    {
    }

//    public function createAction(Request $request)
//    {
//        $this->storyRepository->createStory(
//
//        );
//    }
}
