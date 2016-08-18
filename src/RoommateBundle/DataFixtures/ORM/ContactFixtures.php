<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Entity\Roommate\Contact;
use RoommateBundle\Entity\Roommate\House;

class ContactFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var House $house */
        $house = $this->getReference('house');
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $contact = $this->createContact($house, $faker);
            $manager->persist($contact);
        }

        $manager->flush();
    }

    private function createContact(House $house, Generator $faker)
    {
        return new Contact(
            $house,
            $faker->name,
            $faker->boolean(50) ? $faker->name . '\'s mom' : null,
            $faker->boolean(75) ? new EmailAddress($faker->email) : null,
            $faker->boolean(75) ? $faker->phoneNumber : null
        );
    }

    public function getDependencies()
    {
        return [
            RoommateFixtures::class,
        ];
    }
}
