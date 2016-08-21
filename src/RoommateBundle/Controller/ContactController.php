<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    public function browseAction()
    {
        $roommates = $this->get('roommate.repositories.roommate_repository')->fetchForHouse($this->getCurrentHouseId());
        $contacts = $this->get('roommate.repositories.contact_repository')->fetchForHouse($this->getCurrentHouseId());

        $groupedContacts = [];
        foreach ($contacts as $contact) {
            $letter = strtoupper($contact['name'][0]);

            if (!isset($groupedContacts[$letter])) {
                $groupedContacts[$letter] = [];
            }

            $groupedContacts[$letter][] = $contact;
        }

        return $this->render('RoommateBundle:Contact:browse.html.twig', [
            'groupedContacts' => $groupedContacts,
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
