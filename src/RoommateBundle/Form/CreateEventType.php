<?php

namespace RoommateBundle\Form;

use RoommateBundle\Validator\Constraint\TimeAfter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Time;

class CreateEventType extends AbstractType
{
    const TYPE_FULL_DAY = 'full-day';
    const TYPE_DAY_TIMED = 'day-timed';
    const TYPE_MULTIPLE_DAYS = 'multiple-days';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $groups = [Constraint::DEFAULT_GROUP];

            if ($type = $form->get('type')->getData()) {
                $groups[] = $form->get('type')->getData();
            }

            return $groups;
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Name', 'constraints' => [
            new NotBlank(),
        ]]);
        $builder->add('type', ChoiceType::class, [
            'label' => 'Type',
            'expanded' => true,
            'choices' => [
                'Full day' => self::TYPE_FULL_DAY,
                'Timed' => self::TYPE_DAY_TIMED,
                'Multiple days' => self::TYPE_MULTIPLE_DAYS,
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('single_date', DateType::class, [
            'label' => 'Date',
            'widget' => 'single_text',
            'input' => 'string',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(['groups' => [self::TYPE_FULL_DAY, self::TYPE_DAY_TIMED]]),
                new Date(['groups' => [self::TYPE_FULL_DAY, self::TYPE_DAY_TIMED]]),
            ],
        ]);
        $builder->add('time_start', TimeType::class, [
            'label' => 'Time start',
            'input' => 'string',
            'minutes' => range(0, 60, 5),
            'constraints' => [
                new NotBlank(['groups' => [self::TYPE_DAY_TIMED]]),
                new Time(['groups' => [self::TYPE_DAY_TIMED]]),
            ],
        ]);
        $builder->add('time_end', TimeType::class, [
            'label' => 'Time end',
            'input' => 'string',
            'minutes' => range(0, 60, 5),
            'constraints' => [
                new NotBlank(['groups' => [self::TYPE_DAY_TIMED]]),
                new Time(['groups' => [self::TYPE_DAY_TIMED]]),
                new TimeAfter(['beforeField' => 'time_start', 'groups' => [self::TYPE_DAY_TIMED]]),
            ],
        ]);
        $builder->add('date_start', DateType::class, [
            'label' => 'Start date',
            'widget' => 'single_text',
            'input' => 'string',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(['groups' => [self::TYPE_MULTIPLE_DAYS]]),
                new Date(['groups' => [self::TYPE_MULTIPLE_DAYS]]),
            ],
        ]);
        $builder->add('date_end', DateType::class, [
            'label' => 'End date',
            'widget' => 'single_text',
            'input' => 'string',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(['groups' => [self::TYPE_MULTIPLE_DAYS]]),
                new Date(['groups' => [self::TYPE_MULTIPLE_DAYS]]),
            ],
        ]);
    }
}
