<?php

namespace RoommateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use RoommateBundle\Entity\Cleaning\CleaningJob;
use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Entity\Roommate\House;
use RoommateBundle\Entity\Roommate\Roommate;

class RoommateFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $factory = Factory::create();

        $house = new House();
        $this->setReference('house', $house);
        $manager->persist($house);

        $users = $this->createRoommates($house, $manager, $factory);

        $cleaning = ['Keuken' => '#ff6c60', 'Living' => '#8075c4', 'Overige' => '#a9d86e', 'Sanitair' => '#41cac0'];
        $i = 0;
        foreach ($cleaning as $jobName => $color) {
            $job = new CleaningJob($house, $jobName, $color);
            $manager->persist($job);

            foreach ($users as $u => $user) {
                $user->addCleaningJobIndex($job, ($u + $i) % count($users));
            }
            $i++;
        }

        $manager->flush();
    }

    /** @return array | Roommate[] */
    private function createRoommates(House $house, ObjectManager $manager, Generator $faker)
    {
        $password = '$2a$12$jeAJKlQ94zxVRuqrWn1uq.ZcqcSRP/q7rjP6Bi50TWelX/RXPJVOi'; // brecht

        $users = [];
        $brecht = new Roommate(
            $house,
            new EmailAddress('bonte.brecht@gmail.com'),
            $password,
            'Brecht',
            $faker->phoneNumber
        );
        $manager->persist($brecht);
        $this->setReference('roommate-1', $brecht);
        $users[] = $brecht;

        foreach (['alannah', 'arne', 'tess', 'valerie'] as $i => $name) {
            $roommate = new Roommate(
                $house,
                new EmailAddress($name . '@gmail.com'),
                $password,
                ucfirst($name),
                $faker->phoneNumber
            );
            $manager->persist($roommate);
            $this->setReference('roommate-' . ($i + 2), $roommate);
            $users[] = $roommate;
        }

        return $users;
    }
}
