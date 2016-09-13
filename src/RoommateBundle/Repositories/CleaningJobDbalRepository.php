<?php

namespace RoommateBundle\Repositories;

use Doctrine\DBAL\Connection;
use RoommateBundle\Uuid\HouseId;
use RoommateBundle\Uuid\RoommateId;

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

    /** @return array | string[] */
    public function getCurrentCleaningJobs(RoommateId $roommateId)
    {
        // todo
        $start = new \DateTime($this->cleaningStartYear . '-01 monday');
        $secondsBetween = (new \DateTime)->format('U') - $start->format('U');
        $weeksBetween = floor($secondsBetween / 3600 / 24 / 7);

        $maxSql = '(SELECT max(ind._index) as maxInd, ind.cleaning_job_id FROM cleaning_job_index ind GROUP BY ind.cleaning_job_id)';

        $qb = $this->connection->createQueryBuilder();
        $qb ->select('job.name')
            ->from('cleaning_job', 'job')
            ->join('job', 'cleaning_job_index', 'link', 'link.cleaning_job_id = job.id')
            ->join('job', $maxSql, 'jobMaxInd', 'jobMaxInd.cleaning_job_id = job.id')
            ->where('link.roommate_id = :roommateId')
            ->andWhere('link._index = FLOOR(:weeksBetween % (jobMaxInd.maxInd + 1))')
            ->setParameter('roommateId', (string)$roommateId)
            ->setParameter('weeksBetween', $weeksBetween)
        ;
        $result = $qb->execute();

        $names = [];
        while ($row = $result->fetch()) {
            $names[] = $row['name'];
        }
        return $names;
    }
}
