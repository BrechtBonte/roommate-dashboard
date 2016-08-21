<?php

namespace RoommateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Name', 'constraints' => [
            new NotBlank(),
        ]]);
        $builder->add('nickname', TextType::class, ['label' => 'Nickname', 'required' => false]);
        $builder->add('email', EmailType::class, ['label' => 'E-mail', 'required' => false, 'constraints' => [
            new Email(),
        ]]);
        $builder->add('phoneNumber', TextType::class, ['label' => 'Phone number', 'required' => false, 'attr' => [
            'type' => 'tel',
        ]]);
    }
}
