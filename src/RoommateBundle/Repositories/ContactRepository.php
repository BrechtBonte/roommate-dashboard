<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Uuid\HouseId;

class ContactRepository extends EntityRepository
{
    public function fetchForHouse(HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('contact');
        $qb ->select([
                'contact.name',
                'contact.nickname',
                'contact.email.address as email',
                'contact.phoneNumber',
            ])
            ->andWhere('IDENTITY(contact.house) = :houseId')
            ->orderBy('contact.name')
            ->setParameter('houseId', (string)$houseId)
        ;

        return $qb->getQuery()->getResult();
    }
}
