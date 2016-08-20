<?php

namespace RoommateBundle\Repositories;

use Doctrine\DBAL\Connection;
use RoommateBundle\Uuid\HouseId;

class CleaningJobDbalRepository
{
    private $connection;
    private $cleaningStartYear;

    public function __construct(Connection $connection, $cleaningStartYear)
    {
        $this->connection = $connection;
        $this->cleaningStartYear = $cleaningStartYear;
    }

    public function fetchJobsForHouse(HouseId $houseId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select('job.id', 'job.name', 'job.color')
            ->from('cleaning_job', 'job')
            ->orderBy('job.name')
            ->andWhere('job.house_id = :houseId')
            ->setParameter('houseId', (string)$houseId)
        ;

        return $qb->execute()->fetchAll();
    }

    public function fetchJobAssignees($jobId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb ->select('roommate.name')
            ->from('cleaning_job', 'job')
            ->join('job', 'cleaning_job_index', 'link', 'link.cleaning_job_id = job.id')
            ->join('link', 'roommate', 'roommate', 'link.roommate_id = roommate.id')
            ->andWhere('job.id = :jobId')
            ->orderBy('link._index', 'ASC')
            ->setParameter('jobId', (string)$jobId)
        ;

        return array_column($qb->execute()->fetchAll(), 'name');
    }
}
