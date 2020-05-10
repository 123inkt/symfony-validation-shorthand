<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;

class FloatNumber extends Constraint
{
    public const INVALID_DECIMAL_ERROR = 'f7dfa2ce-5efa-40d4-a8ba-f02968039c28';
    public const INVALID_VALUE_TYPE    = '24e24f8a-fe44-4865-8cec-53c419ac0e87';

    protected static $errorNames = [
        self::INVALID_DECIMAL_ERROR => 'INVALID_DECIMAL_ERROR',
        self::INVALID_VALUE_TYPE    => 'INVALID_VALUE_TYPE',
    ];

    public $message   = '{{ value }} is not a valid decimal.';
    public $allowNull = false;
}
