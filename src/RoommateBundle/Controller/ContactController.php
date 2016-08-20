<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    public function browseAction()
    {
        $roommates = $this->get('roommate.repositories.roommate_repository')->fetchForHouse($this->getCurrentHouseId());

        return $this->render('RoommateBundle:Contact:browse.html.twig', [
            'roommates' => $roommates,
        ]);
    }

    private function getCurrentHouseId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getHouseId();
    }
}
