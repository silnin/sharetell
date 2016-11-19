<?php

namespace Silnin\ShareTell\StoryBundle\Controller;

use DateTime;
use Exception;
use InvalidArgumentException;
use Proxies\__CG__\Silnin\ShareTell\StoryBundle\Entity\Participant;
use Silnin\ShareTell\StoryBundle\Entity\Contribution;
use Silnin\ShareTell\StoryBundle\Entity\Story;
use Silnin\ShareTell\StoryBundle\Repository\ContributionRepository;
use Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository;
use Silnin\ShareTell\StoryBundle\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
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
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param EngineInterface $twigEngine
     * @param StoryRepository $storyRepository
     * @param ParticipantRepository $participantRepository
     * @param TokenStorageInterface $tokenStorage
     * @param ContributionRepository $contributionRepository
     * @param FormFactory $formFactory
     */
    public function __construct(
        EngineInterface $twigEngine,
        StoryRepository $storyRepository,
        ParticipantRepository $participantRepository,
        TokenStorageInterface $tokenStorage,
        ContributionRepository $contributionRepository,
        FormFactory $formFactory
    ) {
        $this->twigEngine = $twigEngine;
        $this->participantRepository = $participantRepository;
        $this->storyRepository = $storyRepository;
        $this->tokenStorage = $tokenStorage;
        $this->contributionRepository = $contributionRepository;
        $this->formFactory = $formFactory;
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
        /** @var User $me */
        $me = $this->tokenStorage->getToken()->getUser();

        // create a task and give it some dummy data for this example
        $story = new Story();
        $story->setTitle('Once upon a time...');
        $story->setCreated(new DateTime());
        $story->setModified(new DateTime());
        $story->setCreator($me);
        $story->setType('public');
        $story->setStatus('active');

        $this->storyRepository->persist($story);

        // add 1st participant
        $this->participantRepository->createParticipant($me, $story);

        return $this->redirect('/dashboard');
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

    public function editAction($reference, Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
//        return new Response(json_encode($requestData, 200));

        $story = $this->storyRepository->getStoryByReference($reference);

        if ($requestData['title']) {
            $story->setTitle($requestData['title']);
        }

        try {
            $this->storyRepository->persist($story);
        } catch (Exception $e) {
            return new Response(
                json_encode(
                    [
                        'error' => $e->getMessage()
                    ]
                ),
                400
            );
        }

        $response = [];
        if ($requestData['title']) {
            $response['title'] = $story->getTitle();
        }

        return new Response(json_encode($response), 200);
    }

    private function getLastContributor(array $participants, array $contributions)
    {
        $nextContributor = null;

        // determine author of last contribution

        /** @var Contribution $lastPost */
        $lastPost = end($contributions);

        if (!$lastPost) {
            return null;
        }

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
