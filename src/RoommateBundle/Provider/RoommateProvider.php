<?php

namespace RoommateBundle\Provider;

use RoommateBundle\Repositories\RoommateRepository;
use RoommateBundle\Uuid\HouseId;
use RoommateBundle\Uuid\RoommateId;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RoommateProvider implements UserProviderInterface
{
    private $roommateRepository;

    public function __construct(RoommateRepository $roommateRepository)
    {
        $this->roommateRepository = $roommateRepository;
    }

    public function loadUserByUsername($emailOrId)
    {
        $userDto = $this->roommateRepository->fetchDtoByEmailOrId($emailOrId);

        if (!$userDto) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $emailOrId)
            );
        }

        return new AuthenticatedUser(
            $userDto['id'],
            $userDto['password'],
            ['ROLE_USER'],
            $userDto['name'],
            RoommateId::fromString($userDto['id']),
            HouseId::fromString($userDto['houseId'])
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof AuthenticatedUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getRoommateId());
    }

    public function supportsClass($class)
    {
        return $class === AuthenticatedUser::class;
    }
}
