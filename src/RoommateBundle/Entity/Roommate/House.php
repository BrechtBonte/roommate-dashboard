<?php

namespace RoommateBundle\Entity\Roommate;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Uuid\HouseId;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class House
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    public function __construct()
    {
        $this->id = (string)HouseId::generate();
    }

    public function getId() : HouseId
    {
        return HouseId::fromString($this->id);
    }
}
