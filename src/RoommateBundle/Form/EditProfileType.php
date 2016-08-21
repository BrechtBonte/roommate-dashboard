<?php

namespace RoommateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('validation_groups', function (FormInterface $form) {

            $groups = ['Default'];

            if ($form->get('password')->getData() || $form->get('oldPassword')->getData()) {
                $groups[] = 'Password';
            }

            return $groups;
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Name', 'constraints' => [
            new NotBlank(),
        ]]);
        $builder->add('email', EmailType::class, ['label' => 'E-mail', 'constraints' => [
            new NotBlank(),
            new Email(),
        ]]);
        $builder->add('phoneNumber', TextType::class, ['label' => 'Phone number', 'required' => false, 'attr' => [
            'type' => 'tel',
        ]]);
        $builder->add('oldPassword', PasswordType::class, ['required' => false, 'constraints' => [
            new UserPassword(['groups' => 'Password']),
        ]]);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
            'first_options' => ['label' => 'New password'],
            'second_options' => ['label' => 'New password (repeat)'],
            'constraints' => [
                new NotBlank(['groups' => 'Password']),
            ],
        ]);
    }
}
