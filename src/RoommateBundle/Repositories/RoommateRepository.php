<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Roommate\Roommate;

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
}
