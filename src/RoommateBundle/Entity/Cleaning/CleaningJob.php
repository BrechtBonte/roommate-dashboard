<?php

namespace RoommateBundle\Entity\Cleaning;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Uuid\CleaningJobId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cleaning_job")
 */
class CleaningJob
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

    public function __construct(string $name)
    {
        $this->id = (string)CleaningJobId::generate();
        $this->name = $name;
    }

    public function getId() : CleaningJobId
    {
        return CleaningJobId::fromString($this->id);
    }

    public function getName()
    {
        return $this->name;
    }
}
