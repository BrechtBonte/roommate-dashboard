<?php

namespace RoommateBundle\Entity\Bulletin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\PollOptionId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\PollOptionRepository")
 * @ORM\Table(name="bulletin_poll_option")
 */
class PollOption
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var BulletinItem
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Bulletin\BulletinItem", inversedBy="options")
     * @ORM\JoinColumn(name="item_id")
     */
    private $item;
    /**
     * @var Collection | PollVote[]
     * @ORM\OneToMany(targetEntity="RoommateBundle\Entity\Bulletin\PollVote", mappedBy="option", cascade={"persist"})
     */
    private $votes;

    public function __construct(string $name, BulletinItem $item)
    {
        $this->id = (string)PollOptionId::generate();
        $this->item = $item;
        $this->name = $name;
        $this->votes = new ArrayCollection();
    }

    /** @return PollOptionId */
    public function getId()
    {
        return PollOptionId::fromString($this->id);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function addVote(Roommate $voter)
    {
        if ($this->item->hasRoommateVoted($voter)) {
            throw new \DomainException('A Roommate can only vote once');
        }
        $this->votes->add(
            new PollVote($this, $voter)
        );
    }

    public function hasRoommateVoted(Roommate $voter)
    {
        return $this->votes->map(
            function (PollVote $vote) {
                return $vote->getVoter();
            }
        )->contains($voter);
    }
}
