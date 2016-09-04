<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\Groceries\GroceryItem;
use RoommateBundle\Entity\Groceries\GroceryList;
use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroceriesController extends Controller
{
    public function viewAction()
    {
        $items = $this->get('roommate.repositories.grocery_item_repository')->fetchUnboughtItems(
            $this->getCurrentHouseId()
        );
        $lists = $this->get('roommate.repositories.grocery_list_repository.dbal')->fetchBoughtListsForHouse(
            $this->getCurrentHouseId()
        );

        return $this->render('RoommateBundle:Groceries:view.html.twig', [
            'items' => $items,
            'lists' => $lists,
        ]);
    }

    public function buyAction(Request $request)
    {
        $itemIds = $request->request->get('items');
        if (!is_array($itemIds)) {
            $request->getSession()->getFlashBag()->add(
                'error',
                'No items were selected'
            );

            return $this->redirectToRoute('groceries_view');
        }
        $groceryItemRepo = $this->get('roommate.repositories.grocery_item_repository');

        $items = array_map(
            function ($itemId) use ($groceryItemRepo) {
                return $groceryItemRepo->find($itemId);
            },
            $itemIds
        );

        $list = new GroceryList(
            $items,
            $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId()),
            $request->request->get('name') ?: null
        );
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($list);
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Items were marked as bought'
        );

        return $this->redirectToRoute('groceries_view');
    }

    public function deleteAction($item, Request $request)
    {
        $item = $this->getCurrentItem($item);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($item);
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Item "' . $item->getName() . '" was deleted'
        );

        return $this->redirectToRoute('groceries_view');
    }

    public function editAction($item, Request $request)
    {
        $item = $this->getCurrentItem($item);

        $item->setName($request->request->get('name'));
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Item "' . $item->getName() . '" was updated'
        );

        return $this->redirectToRoute('groceries_view');
    }

    public function addAction(Request $request)
    {
        $item = new GroceryItem(
            $request->request->get('name'),
            $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId())
        );

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($item);
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Item "' . $item->getName() . '" was added'
        );

        return $this->redirectToRoute('groceries_view');
    }

    private function getCurrentItem($itemId)
    {
        $item = $this->get('roommate.repositories.grocery_item_repository')->findById(
            $itemId,
            $this->getCurrentHouseId()
        );
        if (!$item) {
            throw new NotFoundHttpException();
        }
        return $item;
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
}
