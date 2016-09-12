<?php

namespace RoommateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
        $builder->add('isPoll', CheckboxType::class, ['label' => 'Is poll', 'required' => false]);
        $builder->add('options', CollectionType::class, [
            'label' => 'Poll options',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
        ]);
    }
}
