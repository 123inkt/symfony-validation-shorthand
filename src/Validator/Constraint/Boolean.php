<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Boolean extends Constraint
{
    public const INVALID_BOOLEAN_ERROR = '83f4a7ef-a109-469e-941a-7fa757c73e22';

    protected static $errorNames = [
        self::INVALID_BOOLEAN_ERROR => 'INVALID_BOOLEAN_ERROR',
    ];

    public $message = '{{ value }} is not a valid boolean.';
    public $allowNull = false;
}
