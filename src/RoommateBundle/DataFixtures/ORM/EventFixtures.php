<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use RoommateBundle\DataFixtures\AbstractFixture;
use RoommateBundle\Entity\Event\Event;

class EventFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {

            $dateStart = $faker->dateTimeBetween('-1 month', '+1 month');
            $dateEnd = null;
            if ($faker->boolean(25)) {
                do {
                    $dateEnd = $faker->dateTimeBetween('-1 month', '+1.5 month');
                } while ($dateEnd < $dateStart);
            }

            $event = new Event(
                $faker->words($faker->numberBetween(3, 12), true),
                $this->getReference($this->getRandomReferenceMatching('/^roommate-/')),
                $dateStart,
                $dateEnd
            );

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RoommateFixtures::class,
        ];
    }
}
