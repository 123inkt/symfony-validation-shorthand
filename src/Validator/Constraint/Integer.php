<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Integer extends Constraint
{
    const INVALID_NUMBER_ERROR = '6495a2b1-da7c-43b2-ac4a-0b2398065e5a';
    const INVALID_VALUE_TYPE   = 'e6a0aa5f-48fd-47bf-9a0c-a8ea712e75f5';

    protected static $errorNames = [
        self::INVALID_NUMBER_ERROR => 'INVALID_NUMBER_ERROR',
        self::INVALID_VALUE_TYPE   => 'INVALID_VALUE_TYPE',
    ];

    public $message = '{{ value }} is not a valid number.';
    public $allowNull = false;
}
