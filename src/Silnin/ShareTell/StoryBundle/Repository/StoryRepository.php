<?php

namespace Silnin\ShareTell\StoryBundle\Repository;
use DateTime;
use Silnin\ShareTell\StoryBundle\Entity\Participant;
use Silnin\ShareTell\StoryBundle\Entity\Story;
use Silnin\UserBundle\Entity\User;

/**
 * StoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPublicActiveStories($limit = 20)
    {
        $stories = $this->getEntityManager()->getRepository('SilninShareTellStoryBundle:Story')->findBy(
            [
                'type' => 'public',
                'status' => 'active'
            ]
        );

        return $stories;
    }

    public function getPublicUnjoinedStories(User $user, $limit = 20)
    {
        $allPublic = $this->getPublicActiveStories($limit);
        $filteredGames = [];

        /** @var Story $publicGame */
        foreach ($allPublic as $publicGame) {
            // check if none of the participants is me.

            /** @var Participant $participant */
            foreach ($publicGame->getParticipants() as $participant) {
                if ($participant->getUser()->getId() == $user->getId()) {
                    // don't add this game
                    continue 2;
                }
            }

            $filteredGames[] = $publicGame;
        }

        return $filteredGames;
    }


    public function deleteStoriesFromUser(User $user)
    {
        $stories = $this->getEntityManager()->getRepository('SilninShareTellStoryBundle:Story')->findBy(
            [
                'type' => 'public',
                'status' => 'active',
                'creator' => $user
            ]
        );

        foreach ($stories as $story) {
            $this->getEntityManager()->remove($story);
        }

        $this->getEntityManager()->flush();
    }

    public function getAllStoriesCreatedByUser(User $user)
    {
        return $this->getEntityManager()->getRepository('SilninShareTellStoryBundle:Story')->findBy(
            [
                'creator' => $user
            ]
        );
    }

    /**
     * @param string $title
     * @param string $type
     * @param User $user
     * @return Story
     */
    public function createStory($title, $type, User $user)
    {
        $story = new Story();

        $story->setTitle($title);
        $story->setType($type);
        $story->setCreator($user);
        $story->setStatus('active');
        $story->setModified(new DateTime());
        $story->setCreated(new DateTime());

        $this->getEntityManager()->persist($story);
        $this->getEntityManager()->flush();

        return $story;
    }

    /**
     * Return one Story
     *
     * @param $reference
     * @return Story
     */
    public function getStoryByReference($reference)
    {
        return $this->getEntityManager()->getRepository('SilninShareTellStoryBundle:Story')->findOneBy(
            [
                'reference' => $reference
            ]
        );
    }

    /**
     * Return an array of Stories
     *
     * @param User $user
     * @param bool $filterOutCreated
     * @return array
     */
    public function getJoinedStories(User $user, $filterOutCreated = false)
    {
        $participants = $this->getEntityManager()->getRepository('SilninShareTellStoryBundle:Participant')->findBy(
            [
                'user' => $user
            ]
        );

        $joinedStories = [];
        /** @var Participant $participant */
        foreach ($participants as $participant) {

            if (!$filterOutCreated) {
                $joinedStories[] = $participant->getStory();
                continue;
            }

            if ($participant->getStory()->getCreator()->getId() == $user->getId()) {
                // skip it
                continue;
            }

            $joinedStories[] = $participant->getStory();
        }

        return $joinedStories;
    }

    public function persist(Story $story)
    {
        $this->getEntityManager()->persist($story);
        $this->getEntityManager()->flush();

        return $story;
    }
}
