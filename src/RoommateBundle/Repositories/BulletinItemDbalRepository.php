<?php

namespace RoommateBundle\Repositories;

use Doctrine\DBAL\Connection;
use RoommateBundle\Uuid\HouseId;
use RoommateBundle\Uuid\RoommateId;

class BulletinItemDbalRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchItemsForHouse(HouseId $houseId, RoommateId $currentRoommate)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select([
                'item.id',
                'item.title',
                'item.description',
                'item.date_added',
                'owner.name as owner_name',
                'owner.id as owner_id',
                'IF (seen_by.id IS NULL, 0, 1) as seen',
            ])
            ->from('bulletin_item', 'item')
            ->join('item', 'roommate', 'owner', 'item.owner_id = owner.id')
            ->leftJoin('item', 'bulletin_item_seen_by', 'seen_by', implode(' AND ', [
                'seen_by.bulletin_item = item.id',
                'seen_by.roommate_id = :currentRoommate',
            ]))
            ->andWhere('owner.house_id = :houseId')
            ->andWhere('item.deleted = :deleted')
            ->orderBy('item.date_added', 'DESC')
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('currentRoommate', (string)$currentRoommate)
            ->setParameter('deleted', false)
        ;

        return $qb->execute()->fetchAll();
    }
}
