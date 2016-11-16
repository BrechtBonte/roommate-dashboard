<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use RoommateBundle\DataFixtures\AbstractFixture;
use RoommateBundle\Entity\Debt\Transaction;
use RoommateBundle\Entity\Roommate\Roommate;

class TransactionFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $factory = Factory::create();

        foreach ($this->getReferencesMatching('/^roommate-/') as $reference) {
            /** @var Roommate $roommate */
            $roommate = $this->getReference($reference);

            foreach (range(1, $factory->numberBetween(2, 5)) as $i) {
                $contact = $factory->name;

                foreach (range(1, $factory->numberBetween(1, 6)) as $j) {
                    $transaction = new Transaction(
                        $roommate,
                        $contact,
                        $factory->numberBetween(-9999, 9999),
                        $factory->boolean(75) ? $factory->sentence() : null
                    );
                    $manager->persist($transaction);
                }
            }
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies()
    {
        return [
            RoommateFixtures::class,
        ];
    }
}
