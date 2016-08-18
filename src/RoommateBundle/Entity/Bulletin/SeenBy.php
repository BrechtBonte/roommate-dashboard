<?php

namespace RoommateBundle\Entity\Bulletin;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bulletin_item_seen_by")
 */
class SeenBy
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var BulletinItem
     * @ORM\ManyToOne(targetEntity="BulletinItem", inversedBy="seenBy")
     * @ORM\JoinColumn(name="bulletin_item")
     */
    private $bulletinItem;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     */
    private $roommate;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_seen")
     */
    private $dateSeen;

    public function __construct(BulletinItem $bulletinItem, Roommate $roommate)
    {
        $this->bulletinItem = $bulletinItem;
        $this->roommate = $roommate;
        $this->dateSeen = new \DateTime();
    }

    public function getBulletinItem() : BulletinItem
    {
        return $this->bulletinItem;
    }

    public function getRoommate() : Roommate
    {
        return $this->roommate;
    }

    public function getDateSeen() : \DateTime
    {
        return $this->dateSeen;
    }
}
