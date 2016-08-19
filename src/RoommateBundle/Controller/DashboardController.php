<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function indexAction()
    {
        $items = $this->get('roommate.repositories.bulletin_item_dbal_repository')->fetchItemsForHouse(
            $this->getCurrentHouseId()
        );

        return $this->render('RoommateBundle:Dashboard:view.html.twig', [
            'items' => $items,
        ]);
    }

    private function getCurrentHouseId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getHouseId();
    }
}
