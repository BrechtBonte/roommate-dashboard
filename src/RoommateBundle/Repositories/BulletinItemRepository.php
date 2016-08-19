<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Bulletin\BulletinItem;
use RoommateBundle\Uuid\HouseId;

class BulletinItemRepository extends EntityRepository
{
    public function add(BulletinItem $item)
    {
        $this->_em->persist($item);
    }

    /** @return BulletinItem | null */
    public function findForHouse($itemId, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('item');
        $qb ->innerJoin('item.owner', 'owner')
            ->andWhere('IDENTITY(owner.house) = :houseId')
            ->andWhere('item.id = :itemId')
            ->andWhere('item.deleted = :deleted')
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('itemId', (string)$itemId)
            ->setParameter('deleted', false)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
