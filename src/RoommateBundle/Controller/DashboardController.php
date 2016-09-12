<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\Bulletin\BulletinItem;
use RoommateBundle\Entity\Bulletin\PollOption;
use RoommateBundle\Form\CreateBulletinItemType;
use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DashboardController extends Controller
{
    public function indexAction()
    {
        $items = $this->get('roommate.repositories.bulletin_item_dbal_repository')->fetchItemsForHouse(
            $this->getCurrentHouseId(),
            $this->getCurrentRoommateId()
        );
        $roommates = $this->get('roommate.repositories.roommate_repository')->fetchForHouse($this->getCurrentHouseId());

        return $this->render('RoommateBundle:Dashboard:view.html.twig', [
            'items' => $items,
            'roommates' => $roommates,
        ]);
    }

    public function deleteAction($item, Request $request)
    {
        $item = $this->getCurrentItem($item);
        if (!$item->getOwner()->getId()->equals($this->getCurrentRoommateId())) {
            throw new AccessDeniedException();
        }

        $item->delete();
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Contact deleted'
        );

        return $this->redirectToRoute('dashboard');
    }

    public function acknowledgeAction($item)
    {
        $item = $this->getCurrentItem($item);

        $roommate = $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId());
        $item->markAsSeen($roommate);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('dashboard');
    }

    public function addAction()
    {
        $form = $this->createForm(CreateBulletinItemType::class);

        return $this->render('RoommateBundle:Dashboard:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function processAddAction(Request $request)
    {
        $form = $this->createForm(CreateBulletinItemType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Dashboard:add.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $roommate = $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId());
        $item = new BulletinItem(
            $roommate,
            $form->get('title')->getData(),
            $form->get('description')->getData() ?: null
        );

        if ($options = $form->get('options')->getData()) {
            $optionRepo = $this->get('roommate.repositories.poll_option_repository');
            foreach ($options as $option) {
                $optionRepo->add(
                    new PollOption($option, $item)
                );
            }
        }

        $this->get('roommate.repositories.bulletin_item_repository')->add($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('dashboard');
    }

    public function voteAction($option, Request $request)
    {
        $option = $this->get('roommate.repositories.poll_option_repository')->findById(
            $option,
            $this->getCurrentHouseId()
        );
        if (!$option) {
            throw new NotFoundHttpException();
        }

        $roommate = $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId());
        $option->addVote($roommate);
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Voted "%s" for "%s"', $option->getName(), $option->getItem()->getTitle())
        );

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
