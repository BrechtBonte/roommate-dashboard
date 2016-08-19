<?php

namespace RoommateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateBulletinItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, ['label' => 'Title', 'required' => true, 'constraints' => [
            new NotBlank()
        ]]);
        $builder->add('description', TextareaType::class, ['label' => 'Description', 'required' => false]);
    }
}
