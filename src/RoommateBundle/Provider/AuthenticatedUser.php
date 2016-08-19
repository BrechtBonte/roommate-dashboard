<?php

namespace RoommateBundle\Provider;

use RoommateBundle\Uuid\HouseId;
use RoommateBundle\Uuid\RoommateId;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticatedUser implements UserInterface
{
    private $username;
    private $password;
    private $roles;
    private $displayName;
    private $roommateId;
    private $houseId;

    public function __construct(
        string $username,
        string $password,
        array $roles,
        string $displayName,
        RoommateId $roommateId,
        HouseId $houseId
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->displayName = $displayName;
        $this->roommateId = $roommateId;
        $this->houseId = $houseId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getRoommateId()
    {
        return $this->roommateId;
    }

    public function getHouseId()
    {
        return $this->houseId;
    }

    public function eraseCredentials()
    {
    }
}
