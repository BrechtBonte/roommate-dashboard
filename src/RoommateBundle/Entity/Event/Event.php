<?php

namespace RoommateBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\EventId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\EventRepository")
 * @ORM\Table(name="event")
 */
class Event
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
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     */
    private $owner;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_start")
     */
    private $dateStart;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_end", nullable=true)
     */
    private $dateEnd;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_added")
     */
    private $dateAdded;
    /**
     * @var bool
     * @ORM\Column(type="boolean", name="all_day")
     */
    private $allDay;
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    public function __construct($name, Roommate $owner, \DateTime $dateStart, \DateTime $dateEnd = null)
    {
        $this->id = (string)EventId::generate();
        $this->name = $name;
        $this->owner = $owner;
        $this->setDateRange($dateStart, $dateEnd);
        $this->dateAdded = new \DateTime();
        $this->deleted = false;
    }

    public function getId()
    {
        return EventId::fromString($this->id);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    private function setDateRange(\DateTime $dateStart, \DateTime $dateEnd = null)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->allDay = !$this->dateEnd || $this->dateStart->format('Ymd') !== $this->dateEnd->format('Ymd');
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function isAllDay()
    {
        return $this->allDay;
    }

    public function delete()
    {
        $this->deleted = true;
    }
}
