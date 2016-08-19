<?php

namespace RoommateBundle\Entity\Bulletin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\BulletinItemId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bulletin_item")
 */
class BulletinItem
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     */
    private $owner;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_added")
     */
    private $dateAdded;
    /**
     * @var Collection | SeenBy[]
     * @ORM\OneToMany(targetEntity="RoommateBundle\Entity\Bulletin\SeenBy", mappedBy="bulletinItem", cascade={"persist"})
     */
    private $seenBy;

    public function __construct(Roommate $owner, string $title, string $description = null)
    {
        $this->id = (string)BulletinItemId::generate();
        $this->owner = $owner;
        $this->title = $title;
        $this->description = $description;
        $this->dateAdded = new \DateTime();
        $this->seenBy = new ArrayCollection();

        $this->markAsSeen($owner);
    }

    public function getId() : BulletinItemId
    {
        return BulletinItemId::fromString($this->id);
    }

    public function getOwner() : Roommate
    {
        return $this->owner;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    public function markAsSeen(Roommate $roommate)
    {
        if ($this->wasSeenBy($roommate)) {
            throw new \DomainException('BulletingItem cannot be marked as seen by same Roommate more than once');
        }
        $this->seenBy->add(
            new SeenBy($this, $roommate)
        );
    }

    public function wasSeenBy(Roommate $roommate)
    {
        foreach ($this->seenBy as $seenBy) {
            if ($seenBy->getRoommate() === $roommate) {
                return true;
            }
        }
        return false;
    }
}
