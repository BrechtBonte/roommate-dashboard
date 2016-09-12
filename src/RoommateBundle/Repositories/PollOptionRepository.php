<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Bulletin\PollOption;
use RoommateBundle\Uuid\HouseId;

class PollOptionRepository extends EntityRepository
{
    public function add(PollOption $option)
    {
        $this->_em->persist($option);
    }

    /** @return PollOption | null */
    public function findById($optionId, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('pollOption');
        $qb ->innerJoin('pollOption.item', 'item')
            ->innerJoin('item.owner', 'owner')
            ->andWhere('pollOption = :optionId')
            ->andWhere('IDENTITY(owner.house) = :houseId')
            ->setParameter('optionId', (string)$optionId)
            ->setParameter('houseId', (string)$houseId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
