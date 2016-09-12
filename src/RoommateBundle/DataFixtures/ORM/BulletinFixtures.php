<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use RoommateBundle\DataFixtures\AbstractFixture;
use RoommateBundle\Entity\Bulletin\BulletinItem;
use RoommateBundle\Entity\Bulletin\PollOption;
use RoommateBundle\Entity\Roommate\Roommate;

class BulletinFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $item = $this->createBulletinItem($faker);

            if ($faker->boolean(75)) {
                $this->createRoommateViews($item, $faker);
            } else {
                foreach (range(1, $faker->numberBetween(2, 8)) as $j) {
                    $manager->persist(
                        $this->createPollOption($item, $faker)
                    );
                }
            }

            $manager->persist($item);
        }

        $manager->flush();
    }

    private function createBulletinItem(Generator $faker)
    {
        /** @var Roommate $roommate */
        $roommate = $this->getReference($this->getRandomReferenceMatching('/^roommate-/'));

        $item = new BulletinItem(
            $roommate,
            $faker->words(random_int(3, 7), true),
            $faker->boolean(50) ?
                $faker->sentences(random_int(2, 6), true) :
                null
        );

        return $item;
    }

    private function createRoommateViews(BulletinItem $item, Generator $faker)
    {
        foreach ($this->getReferencesMatching('/^roommate-/') as $reference) {
            /** @var Roommate $roommate */
            $roommate = $this->getReference($reference);

            if ($faker->boolean(25)) {
                try {
                    $item->markAsSeen($roommate);
                } catch (\DomainException $e) {}
            }
        }
    }

    private function createPollOption(BulletinItem $item, Generator $faker)
    {
        $option = new PollOption(
            $faker->word,
            $item
        );

        foreach ($this->getReferencesMatching('/^roommate-/') as $reference) {
            /** @var Roommate $roommate */
            $roommate = $this->getReference($reference);

            if ($faker->boolean(25)) {
                try {
                    $option->addVote($roommate);
                } catch (\DomainException $e) {}
            }
        }

        return $option;
    }

    public function getDependencies()
    {
        return [
            RoommateFixtures::class,
        ];
    }
}
