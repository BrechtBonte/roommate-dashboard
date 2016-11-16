<?php

namespace RoommateBundle\Entity\Debt;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\TransactionId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\TransactionRepository")
 * @ORM\Table(name="transaction", indexes={
 *     @ORM\Index(name="transaction_contact_idx", columns={"roommate_id", "contact"})
 * })
 */
class Transaction
{
    /**
     * @var TransactionId
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var Roommate
     * @ORM\ManyToOne(targetEntity="RoommateBundle\Entity\Roommate\Roommate")
     */
    private $roommate;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $contact;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $amount;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;

    public function __construct(Roommate $roommate, string $contact, int $amount, string $description = null)
    {
        $this->id = (string)TransactionId::generate();
        $this->roommate = $roommate;
        $this->contact = $contact;
        $this->amount = $amount;
        $this->description = $description;
        $this->dateAdded = new \DateTime();
    }

    public function getId() : TransactionId
    {
        return TransactionId::fromString($this->id);
    }

    public function getRoommate() : Roommate
    {
        return $this->roommate;
    }

    public function getContact() : string
    {
        return $this->contact;
    }

    public function getAmount() : int
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDateAdded() : \DateTime
    {
        return $this->dateAdded;
    }
}
