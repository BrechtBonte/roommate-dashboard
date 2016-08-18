<?php

namespace RoommateBundle\Entity\Cleaning;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cleaning_job_index")
 */
class CleaningJobIndex
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate", inversedBy="cleaningJobIndexes")
     */
    private $roommate;
    /**
     * @var CleaningJob
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Cleaning\CleaningJob")
     * @ORM\JoinColumn(name="cleaning_job_id")
     */
    private $cleaningJob;
    /**
     * @var int
     * @ORM\Column(type="integer", name="_index")
     */
    private $index;

    public function __construct(Roommate $roommate, CleaningJob $cleaningJob, int $index)
    {
        $this->roommate = $roommate;
        $this->cleaningJob = $cleaningJob;
        $this->index = $index;
    }

    public function getRoommate() : Roommate
    {
        return $this->roommate;
    }

    public function getCleaningJob() : CleaningJob
    {
        return $this->cleaningJob;
    }

    public function getIndex() : int
    {
        return $this->index;
    }
}
