<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    public function deleteAction($item)
    {
        $item = $this->getCurrentItem($item);
        if (!$item->getOwner()->getId()->equals($this->getCurrentRoommateId())) {
            throw new AccessDeniedException();
        }

        $item->delete();
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        return $this->redirectToRoute('dashboard');
    }

    private function getCurrentHouseId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getHouseId();
    }

    private function getCurrentRoommateId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getRoommateId();
    }

    private function getCurrentItem($itemId)
    {
        $item = $this->get('roommate.repositories.bulletin_item_repository')->findForHouse(
            $itemId,
            $this->getCurrentHouseId()
        );
        if (!$item) {
            throw new NotFoundHttpException();
        }
        return $item;
    }
}
