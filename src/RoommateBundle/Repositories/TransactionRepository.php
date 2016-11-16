<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Debt\Transaction;
use RoommateBundle\Uuid\RoommateId;

class TransactionRepository extends EntityRepository
{
    public function add(Transaction $transaction)
    {
        $this->_em->persist($transaction);
    }

    public function fetchRecentTransactions(RoommateId $roommateId, $limit = 10) : array
    {
        $qb = $this->createQueryBuilder('trans');
        $qb ->select(['trans.contact as name', 'trans.amount', 'trans.description', 'trans.dateAdded'])
            ->andWhere('IDENTITY(trans.roommate) = :roommateId')
            ->orderBy('trans.dateAdded', 'DESC')
            ->setParameter('roommateId', (string)$roommateId)
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function fetchTransactionsForContact(RoommateId $roommateId, string $contact) : array
    {
        $qb = $this->createQueryBuilder('trans');
        $qb ->select(['trans.contact as name', 'trans.amount', 'trans.description', 'trans.dateAdded'])
            ->andWhere('IDENTITY(trans.roommate) = :roommateId')
            ->andWhere('trans.contact = :contact')
            ->orderBy('trans.dateAdded', 'DESC')
            ->setParameter('roommateId', (string)$roommateId)
            ->setParameter('contact', $contact)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function fetchContacts(RoommateId $roommateId) : array
    {
        $qb = $this->createQueryBuilder('trans');
        $qb ->select(['trans.contact as name', 'SUM(trans.amount) as total'])
            ->andWhere('IDENTITY(trans.roommate) = :roommateId')
            ->groupBy('trans.contact')
            ->orderBy('trans.dateAdded', 'DESC')
            ->setParameter('roommateId', (string)$roommateId)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
