<?php

namespace Silnin\ShareTell\StoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Silnin\UserBundle\Entity\User;

/**
 * Participant
 *
 * @ORM\Table(name="participant")
 * @ORM\Entity(repositoryClass="Silnin\ShareTell\StoryBundle\Repository\ParticipantRepository")
 */
class Participant implements JsonSerializable
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

    public function getStory()
    {
        return $this->story;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param mixed $story
     */
    public function setStory($story)
    {
        $this->story = $story;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @param bool $cascade
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize($cascade = true)
    {
        $result = [];
        $result['id'] = $this->getId();
        $result['joined_at'] = $this->getJoined()->format('Y-m-d H:i:s');
        $result['status'] = $this->getStatus();

        if ($cascade) {
            $result['user'] = $this->getUser()->jsonSerialize();
            $result['story'] = $this->getStory()->jsonSerialize(false);
        } else {
            $result['user_id'] = $this->getUser()->getId();
            $result['story_id'] = $this->getStory()->getId();
        }

        return json_encode($result);
    }
}
