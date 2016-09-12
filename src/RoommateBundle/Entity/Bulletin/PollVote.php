<?php

namespace RoommateBundle\Entity\Bulletin;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bulletin_poll_vote")
 */
class PollVote
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var PollOption
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Bulletin\PollOption", inversedBy="votes")
     */
    private $option;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     */
    private $voter;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_voted")
     */
    private $dateVoted;

    public function __construct(PollOption $option, Roommate $voter)
    {
        $this->option = $option;
        $this->voter = $voter;
        $this->dateVoted = new \DateTime();
    }

    public function getOption()
    {
        return $this->option;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function getDateVoted()
    {
        return $this->dateVoted;
    }
}
