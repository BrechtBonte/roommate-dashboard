<?php

namespace RoommateBundle\Entity\Cleaning;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\House;
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
     * @var House
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\House")
     */
    private $house;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $color;

    public function __construct(House $house, string $name, string $color)
    {
        $this->id = (string)CleaningJobId::generate();
        $this->house = $house;
        $this->name = $name;
        $this->color = $color;
    }

    public function getId() : CleaningJobId
    {
        return CleaningJobId::fromString($this->id);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getColor() : string
    {
        return $this->color;
    }
}
