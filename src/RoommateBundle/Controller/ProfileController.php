<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\EmailAddress;
use RoommateBundle\Form\EditProfileType;
use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class ProfileController extends Controller
{
    public function editAction()
    {
        $roommate = $this->getCurrentRoommate();
        $form = $this->createForm(EditProfileType::class, [
            'name' => $roommate->getName(),
            'email' => $roommate->getEmail()->getAddress(),
            'phoneNumber' => $roommate->getPhoneNumber(),
        ]);

        return $this->render('RoommateBundle:Profile:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function processEditAction(Request $request)
    {
        $form = $this->createForm(EditProfileType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Profile:edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $roommate = $this->getCurrentRoommate();
        $roommate->setName($form->get('name')->getData());
        $roommate->setEmail(new EmailAddress($form->get('email')->getData()));
        $roommate->setPhoneNumber($form->get('phoneNumber')->getData() ?: null);

        if ($form->get('password')->getData()) {
            /** @var PasswordEncoderInterface $encoder */
            $encoder = $this->get('security.encoder_factory')->getEncoder(AuthenticatedUser::class);
            $roommate->setPassword($encoder->encodePassword($form->get('password')->getData(), null));
        }

        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->getFlashBag()->add(
            'success',
            'Profile updated'
        );

        return $this->redirectToRoute('profile_edit');
    }

    private function getCurrentRoommate()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $this->get('roommate.repositories.roommate_repository')->find($user->getRoommateId());
    }
}
