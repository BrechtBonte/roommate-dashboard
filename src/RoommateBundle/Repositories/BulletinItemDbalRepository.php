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
            ->setMaxResults(50)
        ;

        $items = $qb->execute()->fetchAll();
        $pollOptions = $this->fetchPollOptionsByIds(array_column($items, 'id'), $currentRoommate);

        return array_map(
            function (array $item) use ($pollOptions, $currentRoommate) {
                $item['options'] = $pollOptions[$item['id']] ?? [];
                $item['hasVoted'] = (bool)array_filter(array_column($item['options'], 'hasVoted'));
                return $item;
            },
            $items
        );
    }

    private function fetchPollOptionsByIds(array $itemIds, RoommateId $currentRoommate)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select([
                'item.id as item_id',
                'opt.id',
                'opt.name',
                'GROUP_CONCAT(voter.name SEPARATOR ";") as voters',
                'GROUP_CONCAT(voter.id SEPARATOR ";") as voter_ids',
            ])
            ->from('bulletin_poll_option', 'opt')
            ->join('opt', 'bulletin_item', 'item', 'opt.item_id = item.id')
            ->leftJoin('opt', 'bulletin_poll_vote', 'vote', 'vote.option_id = opt.id')
            ->leftJoin('vote', 'roommate', 'voter', 'vote.voter_id = voter.id')
            ->where($qb->expr()->in('item.id', ':itemIds'))
            ->orderBy('vote.date_voted')
            ->groupBy('opt.id')
            ->setParameter('itemIds', $itemIds, Connection::PARAM_STR_ARRAY)
        ;
        $result = $qb->execute();

        $options = [];
        while ($row = $result->fetch()) {
            if (!isset($options[$row['item_id']])) {
                $options[$row['item_id']] = [];
            }
            $row['voters'] = $row['voters'] ? explode(';', $row['voters']) : [];
            $row['hasVoted'] = in_array((string)$currentRoommate, explode(';', $row['voter_ids']), true);
            unset($row['voter_ids']);

            $options[$row['item_id']][] = $row;
        }

        foreach ($options as &$sortableOptions) {
            array_multisort(
                array_map(
                    function ($option) {
                        return count($option['voters']);
                    },
                    $sortableOptions
                ),
                SORT_DESC,
                $sortableOptions
            );
        }

        return $options;
    }
}
