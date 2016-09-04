<?php

namespace RoommateBundle\Entity\Groceries;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;

/**
 * @ORM\Entity()
 * @ORM\Table(name="grocery_list")
 */
class GroceryList
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    /**
     * @var Collection | GroceryItem[]
     * @ORM\OneToMany(targetEntity="RoommateBundle\Entity\Groceries\GroceryItem", mappedBy="list")
     */
    private $items;
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

    public function __construct(array $items, Roommate $addedBy, string $name = null)
    {
        $this->name = $name;
        $this->items = new ArrayCollection();
        $this->addedBy = $addedBy;
        $this->dateAdded = new \DateTime();

        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItems() : Collection
    {
        return $this->items;
    }

    public function getAddedBy() : Roommate
    {
        return $this->addedBy;
    }

    public function getDateAdded() : \DateTime
    {
        return $this->dateAdded;
    }

    private function addItem(GroceryItem $item)
    {
        $item->setList($this);
        $this->items->add($item);
    }
}
