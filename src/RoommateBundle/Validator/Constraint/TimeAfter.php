<?php

namespace RoommateBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class TimeAfter extends Constraint
{
    public $message = 'This time should be after {hour}';
    public $beforeField;

    public function getRequiredOptions()
    {
        return ['beforeField'];
    }
}
