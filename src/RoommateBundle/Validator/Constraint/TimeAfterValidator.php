<?php

namespace RoommateBundle\Validator\Constraint;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class TimeAfterValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TimeAfter) {
            throw new UnexpectedTypeException($constraint, TimeAfter::class);
        }
        if (!$value) {
            return;
        }

        /** @var Form $form */
        $form = $this->context->getRoot();
        if (!$form->has($constraint->beforeField)) {
            throw new InvalidArgumentException('Unknown field: ' . $constraint->beforeField);
        }
        $targetTime = $form->get($constraint->beforeField)->getData();
        if (!$targetTime) {
            return;
        }

        if ((int)preg_replace('/[^\d]/', '', $value) <= (int)preg_replace('/[^\d]/', '', $targetTime)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{hour}', $targetTime)
                ->addViolation();
        }
    }
}
