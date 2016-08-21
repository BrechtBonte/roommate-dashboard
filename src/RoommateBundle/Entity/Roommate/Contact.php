<?php

namespace RoommateBundle\Entity\Roommate;

use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Uuid\ContactId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\ContactRepository")
 * @ORM\Table(name="contact")
 */
class Contact
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var House
     * @ORM\ManyToOne(targetEntity="House")
     */
    private $house;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $nickname;
    /**
     * @var EmailAddress
     * @ORM\Embedded(class="RoommateBundle\Entity\EmailAddress")
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(type="string", name="phone_number", nullable=true)
     */
    private $phoneNumber;
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    public function __construct(
        House $house,
        string $name,
        string $nickname = null,
        EmailAddress $email = null,
        string $phoneNumber = null
    ) {
        $this->id = (string)ContactId::generate();
        $this->house = $house;
        $this->name = $name;
        $this->nickname = $nickname;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->deleted = false;
    }

    public function getId() : ContactId
    {
        return ContactId::fromString($this->id);
    }

    public function getHouse() : House
    {
        return $this->house;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname = null)
    {
        $this->nickname = $nickname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(EmailAddress $email = null)
    {
        $this->email = $email;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber = null)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function delete()
    {
        $this->deleted = true;
    }
}
