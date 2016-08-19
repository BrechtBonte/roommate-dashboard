<?php

namespace RoommateBundle\Repositories;

use Doctrine\DBAL\Connection;
use RoommateBundle\Uuid\HouseId;

class BulletinItemDbalRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchItemsForHouse(HouseId $houseId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select([
                'item.id',
                'item.title',
                'item.description',
                'item.date_added',
                'owner.name as owner_name',
                'owner.id as owner_id',
            ])
            ->from('bulletin_item', 'item')
            ->join('item', 'roommate', 'owner', 'item.owner_id = owner.id')
            ->andWhere('owner.house_id = :houseId')
            ->andWhere('item.deleted = :deleted')
            ->orderBy('item.date_added', 'DESC')
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('deleted', false)
        ;

        return $qb->execute()->fetchAll();
    }
}
