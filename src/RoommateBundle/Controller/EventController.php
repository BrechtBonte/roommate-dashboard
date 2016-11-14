<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\Event\Event;
use RoommateBundle\Form\CreateEventType;
use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends Controller
{
    public function viewAction()
    {
        $events = $this->get('roommate.repositories.event_repository')->getEventsAfter(
            new \DateTime('-1 month'),
            $this->getCurrentHouseId()
        );

        return $this->render('RoommateBundle:Event:view.html.twig', [
            'events' => $events,
        ]);
    }

    public function addAction()
    {
        $form = $this->createForm(CreateEventType::class);

        return $this->render('RoommateBundle:Event:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function processAddAction(Request $request)
    {
        $form = $this->createForm(CreateEventType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Event:add.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $dateStart = null;
        $dateEnd = null;
        switch ($form->get('type')->getData()) {
            case CreateEventType::TYPE_FULL_DAY:
                $dateStart = \DateTime::createFromFormat('Y-m-d', $form->get('single_date')->getData());
                break;
            case CreateEventType::TYPE_DAY_TIMED:
                $dateStart = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf(
                    '%s %s',
                    $form->get('single_date')->getData(),
                    $form->get('time_start')->getData()
                ));
                $dateEnd = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf(
                    '%s %s',
                    $form->get('single_date')->getData(),
                    $form->get('time_end')->getData()
                ));
                break;
            case CreateEventType::TYPE_MULTIPLE_DAYS:
                $dateStart = \DateTime::createFromFormat('Y-m-d', $form->get('date_start')->getData());
                $dateEnd = \DateTime::createFromFormat('Y-m-d', $form->get('date_end')->getData());
                break;
        }

        $roommate = $this->get('roommate.repositories.roommate_repository')->find($this->getCurrentRoommateId());
        $event = new Event(
            $form->get('name')->getData(),
            $roommate,
            $dateStart,
            $dateEnd
        );

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($event);
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Event "%s" was added', $event->getName())
        );

        return $this->redirectToRoute('events_browse');
    }

    public function deleteAction($event, Request $request)
    {
        $event = $this->get('roommate.repositories.event_repository')->findForRoommate(
            $event,
            $this->getCurrentRoommateId()
        );
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $event->delete();
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Event "%s" was deleted', $event->getName())
        );

        return $this->redirectToRoute('events_browse');
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
