<?php

namespace RoommateBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use RoommateBundle\Entity\Roommate\Contact;
use RoommateBundle\Uuid\HouseId;

class ContactRepository extends EntityRepository
{
    public function add(Contact $contact)
    {
        $this->_em->persist($contact);
    }

    /** @return Contact | null */
    public function findForHouse($contactId, HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('contact');
        $qb ->andWhere('contact.id = :contactId')
            ->andWhere('IDENTITY(contact.house) = :houseId')
            ->andWhere('contact.deleted = :deleted')
            ->setParameter('contactId', (string)$contactId)
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('deleted', false)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function fetchForHouse(HouseId $houseId)
    {
        $qb = $this->createQueryBuilder('contact');
        $qb ->select([
                'contact.id',
                'contact.name',
                'contact.nickname',
                'contact.email.address as email',
                'contact.phoneNumber',
            ])
            ->andWhere('IDENTITY(contact.house) = :houseId')
            ->andWhere('contact.deleted = :deleted')
            ->orderBy('contact.name')
            ->setParameter('houseId', (string)$houseId)
            ->setParameter('deleted', false)
        ;

        return $qb->getQuery()->getResult();
    }
}
