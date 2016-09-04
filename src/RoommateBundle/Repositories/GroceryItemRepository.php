<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Groceries\GroceryItem;
use RoommateBundle\Uuid\HouseId;

class GroceryItemRepository extends EntityRepository
{
    /** @return array | GroceryItem[] */
    public function fetchUnboughtItems(HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('item');
        $qb ->select(['item.id', 'item.name'])
            ->innerJoin('item.addedBy', 'roommate')
            ->where('item.list IS NULL')
            ->andWhere('IDENTITY(roommate.house) = :houseId')
            ->orderBy('item.dateAdded', 'ASC')
            ->setParameter('houseId', (string)$houseId)
        ;

        return $qb->getQuery()->getResult();
    }

    /** @return GroceryItem | null */
    public function findById($itemId, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('item');
        $qb ->innerJoin('item.addedBy', 'roommate')
            ->where('item.list IS NULL')
            ->andWhere('IDENTITY(roommate.house) = :houseId')
            ->andWhere('item.id = :itemId')
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('itemId', (string)$itemId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
