<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;

class IntegerNumber extends Constraint
{
    /** @var string */
    public const INVALID_NUMBER_ERROR = 'fd2ba819-b3ad-4643-ae18-137817d63de9';
    /** @var string */
    public const INVALID_VALUE_TYPE = 'af5ee700-4222-468a-8ff3-c3b394fc500b';

    protected const ERROR_NAMES = [
        self::INVALID_NUMBER_ERROR => 'INVALID_NUMBER_ERROR',
        self::INVALID_VALUE_TYPE   => 'INVALID_VALUE_TYPE',
    ];

    public string $message = '{{ value }} is not a valid number.';
}
