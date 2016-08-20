<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Uuid\HouseId;

/**
 * @method Roommate find($id, $lockMode = null, $lockVersion = null)
 */
class RoommateRepository extends EntityRepository
{
    /** @return array | null */
    public function fetchDtoByEmailOrId($emailOrId)
    {
        $qb = $this->createQueryBuilder('roommate');
        $qb ->select([
                'roommate.id',
                'roommate.name',
                'roommate.password',
                'IDENTITY(roommate.house) as houseId',
            ])
            ->andWhere($qb->expr()->orX(
                'roommate.email.address = :emailOrId',
                'roommate.id = :emailOrId'
            ))
            ->setParameter('emailOrId', (string)$emailOrId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function fetchForHouse(HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('roommate');
        $qb ->select([
                'roommate.name',
                'roommate.email.address as email',
                'roommate.phoneNumber',
            ])
            ->andWhere('IDENTITY(roommate.house) = :houseId')
            ->orderBy('roommate.name', 'ASC')
            ->setParameter('houseId', (string)$houseId)
        ;

        return $qb->getQuery()->getResult();
    }
}
