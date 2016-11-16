<?php

namespace RoommateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('multiplier', ChoiceType::class, [
            'choices' => [
                'I lent (+)' => '1',
                'I loaned (-)' => '-1',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('amount', TextType::class, ['constraints' => [
            new NotBlank(),
            new Callback(function ($amount, ExecutionContextInterface $context) {
                $amount = preg_replace('/[^0-9,]/', '', $amount);
                $parts = explode(',', $amount);

                if (!$parts[0] && !($parts[1] ?? 0)) {
                    $context->buildViolation('Invalid monetary amount')
                        ->addViolation();
                }
            }),
        ]]);
        $builder->add('to', TextType::class, ['constraints' => [
            new NotBlank(),
        ]]);
        $builder->add('description', TextType::class, ['required' => false]);
    }
}
