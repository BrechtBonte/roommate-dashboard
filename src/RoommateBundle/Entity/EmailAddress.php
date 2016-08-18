<?php

namespace RoommateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Embeddable */
class EmailAddress
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    public function __construct(string $emailAddress)
    {
        if (!$this->isValid($emailAddress)) {
            throw new \InvalidArgumentException('Invalid email address: ' . $emailAddress);
        }
        $this->address = $emailAddress;
    }

    private function isValid($emailAddress) : bool
    {
        return filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
    }

    public function getAddress() : string
    {
        return $this->address;
    }
}
