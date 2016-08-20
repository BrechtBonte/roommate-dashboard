<?php

namespace RoommateBundle\Entity\Roommate;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RoommateBundle\Entity\Cleaning\CleaningJob;
use RoommateBundle\Entity\Cleaning\CleaningJobIndex;
use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Uuid\RoommateId;

/**
 * @ORM\Entity(repositoryClass="RoommateBundle\Repositories\RoommateRepository")
 * @ORM\Table(name="roommate")
 */
class Roommate
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
     * @var EmailAddress
     * @ORM\Embedded(class="RoommateBundle\Entity\EmailAddress")
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", name="phone_number", nullable=true)
     */
    private $phoneNumber;
    /**
     * @var Collection | CleaningJobIndex
     * @ORM\OneToMany(targetEntity="RoommateBundle\Entity\Cleaning\CleaningJobIndex", mappedBy="roommate", cascade={"persist"})
     * @ORM\JoinColumn(name="cleaning_job_index")
     */
    private $cleaningJobIndexes;

    public function __construct(
        House $house,
        EmailAddress $email,
        string $password,
        string $name = null,
        string $phoneNumber = null
    ) {
        $this->id = (string)RoommateId::generate();
        $this->house = $house;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->phoneNumber = $phoneNumber;
        $this->cleaningJobIndexes = new ArrayCollection();
    }

    public function getId() : RoommateId
    {
        return RoommateId::fromString($this->id);
    }

    public function getHouse() : House
    {
        return $this->house;
    }

    public function getEmail() : EmailAddress
    {
        return $this->email;
    }

    public function setEmail(EmailAddress $email)
    {
        $this->email = $email;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name = null)
    {
        $this->name = $name;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber = null)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function addCleaningJobIndex(CleaningJob $cleaningJob, int $index)
    {
        $this->cleaningJobIndexes->add(
            new CleaningJobIndex($this, $cleaningJob, $index)
        );
    }
}
