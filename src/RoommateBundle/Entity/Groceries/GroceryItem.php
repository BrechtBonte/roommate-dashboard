<?php

namespace RoommateBundle\Entity\Groceries;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\GroceryItemId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\GroceryItemRepository")
 * @ORM\Table(name="grocery_item")
 */
class GroceryItem
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
     * @var GroceryList
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Groceries\GroceryList", inversedBy="items")
     */
    private $list;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     * @ORM\JoinColumn(name="added_by")
     */
    private $addedBy;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="date_added")
     */
    private $dateAdded;

    public function __construct(string $name, Roommate $addedBy)
    {
        $this->id = (string)GroceryItemId::generate();
        $this->name = $name;
        $this->addedBy = $addedBy;
        $this->dateAdded = new \DateTime();
    }

    public function getId() : GroceryItemId
    {
        return GroceryItemId::fromString($this->id);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getList() : GroceryList
    {
        return $this->list;
    }

    public function setList(GroceryList $list)
    {
        $this->list = $list;
    }

    public function getAddedBy() : Roommate
    {
        return $this->addedBy;
    }

    public function getDateAdded() : \DateTime
    {
        return $this->dateAdded;
    }
}
