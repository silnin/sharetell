<?php

namespace Silnin\ShareTell\StoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Silnin\UserBundle\Entity\User;

/**
 * Story
 *
 * @ORM\Table(name="story")
 * @ORM\Entity(repositoryClass="Silnin\ShareTell\StoryBundle\Repository\StoryRepository")
 */
class Story implements JsonSerializable
{
    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';
    const STATUS_SUSPENDED = 'suspended';

    const TYPE_PUBLIC = 'public';
    const TYPE_PRIVATE = 'private';
    const TYPE_HIDDEN = 'hidden';

    /**
     *
     */
    public function __construct()
    {
        $this->reference = $this->generateRandomID();
    }

    function generateRandomID()
    {
        return hash('crc32b', Uuid::uuid4());
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Silnin\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    protected $creator;

    /**
     * @ORM\OneToMany(targetEntity="Silnin\ShareTell\StoryBundle\Entity\Participant", mappedBy="story")
     */
    private $participants = [];

    /**
     * @ORM\OneToMany(targetEntity="Silnin\ShareTell\StoryBundle\Entity\Contribution", mappedBy="story")
     */
    private $contributions;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="min_words", type="integer")
     */
    private $minWords = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="max_words", type="integer")
     */
    private $maxWords = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;


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
     * Set title
     *
     * @param string $title
     *
     * @return Story
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Story
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Story
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

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Story
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return Story
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }


    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getMinWords()
    {
        return $this->minWords;
    }

    /**
     * @param int $minWords
     */
    public function setMinWords($minWords)
    {
        if ($minWords === null) {
            return;
        }
        $this->minWords = $minWords;
    }

    /**
     * @return int
     */
    public function getMaxWords()
    {
        return $this->maxWords;
    }

    /**
     * @param int $maxWords
     */
    public function setMaxWords($maxWords)
    {
        if ($maxWords === null) {
            return;
        }
        $this->maxWords = $maxWords;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @param bool $cascade
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize($cascade = true)
    {
        $result = [];

        $result['id'] = $this->id;
        $result['title'] = $this->title;
        $result['status'] = $this->status;
        $result['type'] = $this->type;
        $result['min_words'] = $this->minWords;
        $result['max_words'] = $this->maxWords;
        $result['reference'] = $this->reference;
        $result['created'] = $this->created->format('Y-m-d H:i:s');
        $result['modified'] = $this->modified->format('Y-m-d H:i:s');
        $result['contributions'] = [];
        $result['participants'] = [];


        /** @var Participant $participant */
        foreach ($this->participants as $participant) {
            if ($cascade) {
                $result['participants'][] = $participant->jsonSerialize(false);
            } else {
                $result['participants'][] = array('id' => $participant->getId());
            }
        }

        /** @var Contribution $contribution */
        foreach ($this->contributions as $contribution) {
            if ($cascade) {
                $result['contributions'][] = $contribution->jsonSerialize(false);
            } else {
                $result['contributions'][] = array('id' => $contribution->getId());
            }
        }

        if ($cascade) {
            $result['creator'] = $this->creator->jsonSerialize();
        } else {
            $result['creator_id'] = $this->creator->getId();
        }

        return json_encode($result);
    }

    /**
     * @return mixed
     */
    public function getContributions()
    {
        return $this->contributions;
    }

    /**
     * @param mixed $contributions
     */
    public function setContributions($contributions)
    {
        $this->contributions = $contributions;
    }

    public function toString() {
        return (string) $this->id;
    }
}
