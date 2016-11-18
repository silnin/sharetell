<?php

namespace Silnin\ShareTell\StoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Silnin\UserBundle\Entity\User;

/**
 * Participant
 *
 * @ORM\Table(name="participant")
 * @ORM\Entity(repositoryClass="Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="joined", type="datetime")
     */
    private $joined;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var Story
     *
     * @ORM\ManyToOne(targetEntity="Silnin\ShareTell\StoryBundle\Entity\Story", inversedBy="participants")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    protected $story;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Silnin\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set joined
     *
     * @param \DateTime $joined
     *
     * @return Participant
     */
    public function setJoined($joined)
    {
        $this->joined = $joined;

        return $this;
    }

    /**
     * Get joined
     *
     * @return \DateTime
     */
    public function getJoined()
    {
        return $this->joined;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Participant
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getName()
    {
        return $this->user->getUsername();
    }

    public function getEmail()
    {
        return $this->user->getEmail();
    }

    public function getStory()
    {
        return $this->story;
    }
}

