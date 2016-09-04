<?php

namespace RoommateBundle\Repositories;

use Doctrine\DBAL\Connection;
use RoommateBundle\Uuid\HouseId;

class GroceryListDbalRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchBoughtListsForHouse(HouseId $houseId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select([
                'list.name',
                'roommate.name as added_by',
                'list.date_added',
                'GROUP_CONCAT(item.name SEPARATOR ";") as item_names',
            ])
            ->from('grocery_list', 'list')
            ->join('list', 'grocery_item', 'item', 'item.list_id = list.id')
            ->join('list', 'roommate', 'roommate', 'list.added_by = roommate.id')
            ->where('roommate.house_id = :houseId')
            ->groupBy('list.id')
            ->orderBy('list.date_added', 'DESC')
            ->setParameter('houseId', (string)$houseId)
            ->setMaxResults(10);

        return array_map(
            function (array $row) {
                $row['items'] = explode(';', $row['item_names']);
                return $row;
            },
            $qb->execute()->fetchAll()
        );
    }
}
