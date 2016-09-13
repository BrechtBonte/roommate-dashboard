<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Event\Event;
use RoommateBundle\Uuid\HouseId;
use RoommateBundle\Uuid\RoommateId;

class EventRepository extends EntityRepository
{
    public function add(Event $event)
    {
        $this->_em->persist($event);
    }

    public function getEventsAfter(\DateTime $threshold, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('event');
        $qb ->select([
                'event.id',
                'event.name',
                'event.dateStart',
                'event.dateEnd',
                'event.allDay',
                'owner.id as ownerId',
            ])
            ->join('event.owner', 'owner')
            ->andWhere('event.deleted = :deleted')
            ->andWhere('IDENTITY(owner.house) = :houseId')
            ->andWhere('coalesce(event.dateEnd, event.dateStart) >= :threshold')
            ->setParameter('deleted', false)
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('threshold', $threshold->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getResult();
    }

    /** @return Event | null */
    public function findForRoommate($eventId, RoommateId $roommateId)
    {
        $qb = $this->createQueryBuilder('event');
        $qb ->andWhere('event.deleted = :deleted')
            ->andWhere('IDENTITY(event.owner) = :roommateId')
            ->andWhere('event.id = :eventId')
            ->setParameter('deleted', false)
            ->setParameter('roommateId', (string)$roommateId)
            ->setParameter('eventId', (string)$eventId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function countEventsAfter(\DateTime $threshold, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('event');
        $qb ->select('count(event.id)')
            ->join('event.owner', 'owner')
            ->andWhere('event.deleted = :deleted')
            ->andWhere('IDENTITY(owner.house) = :houseId')
            ->andWhere('coalesce(event.dateEnd, event.dateStart) >= :threshold')
            ->setParameter('deleted', false)
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('threshold', $threshold->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getNearestEventAfter(\DateTime $threshold, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('event');
        $qb ->select('min(coalesce(event.dateEnd, event.dateStart))')
            ->join('event.owner', 'owner')
            ->andWhere('event.deleted = :deleted')
            ->andWhere('IDENTITY(owner.house) = :houseId')
            ->andWhere('coalesce(event.dateEnd, event.dateStart) >= :threshold')
            ->setParameter('deleted', false)
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('threshold', $threshold->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}
