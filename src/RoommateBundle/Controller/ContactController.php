<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Entity\Roommate\Contact;
use RoommateBundle\Entity\Roommate\House;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Form\ContactType;
use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function addAction()
    {
        $form = $this->createForm(ContactType::class);

        return $this->render('RoommateBundle:Contact:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function processAddAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Contact:edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $manager = $this->getDoctrine()->getManager();
        $contact = new Contact(
            $manager->find(House::class, $this->getCurrentHouseId()),
            $form->get('name')->getData(),
            $form->get('nickname')->getData() ?: null,
            $form->get('email')->getData() ? new EmailAddress($form->get('email')->getData()) : null,
            $form->get('phoneNumber')->getData() ?: null
        );
        $this->get('roommate.repositories.contact_repository')->add($contact);
        $manager->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Contact "%s" added', $contact->getName())
        );

        return $this->redirectToRoute('contacts_edit', ['contact' => $contact->getId()]);
    }

    public function editAction($contact)
    {
        $contact = $this->getCurrentContact($contact);
        $form = $this->createForm(ContactType::class, [
            'name' => $contact->getName(),
            'nickname' => $contact->getNickname(),
            'email' => (string)$contact->getEmail(),
            'phoneNumber' => $contact->getPhoneNumber(),
        ]);

        return $this->render('RoommateBundle:Contact:edit.html.twig', [
            'contact' => $contact->getName(),
            'form' => $form->createView(),
        ]);
    }

    public function processEditAction($contact, Request $request)
    {
        $contact = $this->getCurrentContact($contact);
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Contact:edit.html.twig', [
                'contact' => $contact->getName(),
                'form' => $form->createView(),
            ]);
        }

        $contact->setName($form->get('name')->getData());
        $contact->setNickname($form->get('nickname')->getData() ?: null);
        $contact->setEmail($form->get('email')->getData() ? new EmailAddress($form->get('email')->getData()) : null);
        $contact->setPhoneNumber($form->get('phoneNumber')->getData() ?: null);
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Contact "%s" updated', $contact->getName())
        );

        return $this->redirectToRoute('contacts_edit', ['contact' => $contact->getId()]);
    }

    public function deleteAction($contact, Request $request)
    {
        $contact = $this->getCurrentContact($contact);

        $contact->delete();
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('Contact "%s" deleted', $contact->getName())
        );

        return $this->redirectToRoute('contacts_browse');
    }

    private function getCurrentHouseId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getHouseId();
    }

    private function getCurrentContact($contactId)
    {
        $contact = $this->get('roommate.repositories.contact_repository')->findForHouse(
            $contactId,
            $this->getCurrentHouseId()
        );
        if (!$contact) {
            throw new NotFoundHttpException();
        }
        return $contact;
    }
}
