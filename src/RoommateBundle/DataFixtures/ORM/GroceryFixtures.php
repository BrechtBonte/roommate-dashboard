<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use RoommateBundle\DataFixtures\AbstractFixture;
use RoommateBundle\Entity\Groceries\GroceryItem;
use RoommateBundle\Entity\Groceries\GroceryList;
use RoommateBundle\Entity\Roommate\Roommate;

class GroceryFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // create bought lists
        foreach (range(1, 10) as $i) {
            $items = [];
            foreach (range(1, $faker->numberBetween(1, 10)) as $j) {
                $item = new GroceryItem(
                    $faker->word,
                    $this->getRandomRoommate()
                );
                $manager->persist($item);

                $items[] = $item;
            }

            $list = new GroceryList(
                $items,
                $this->getRandomRoommate(),
                $faker->boolean(50) ? $faker->word : null
            );
            $manager->persist($list);
        }

        foreach (range(1, $faker->numberBetween(3, 10)) as $i) {
            $manager->persist(new GroceryItem(
                $faker->word,
                $this->getRandomRoommate()
            ));
        }

        $manager->flush();
    }

    private function getRandomRoommate() : Roommate
    {
        return $this->getReference($this->getRandomReferenceMatching('/^roommate-/'));
    }

    public function getDependencies()
    {
        return [
            RoommateFixtures::class,
        ];
    }
}
