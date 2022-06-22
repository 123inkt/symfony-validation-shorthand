<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;

class BooleanValue extends Constraint
{
    public const INVALID_BOOLEAN_ERROR = '83f4a7ef-a109-469e-941a-7fa757c73e22';

    /** @var mixed */
    protected static $errorNames = [
        self::INVALID_BOOLEAN_ERROR => 'INVALID_BOOLEAN_ERROR',
    ];

    /** @var string */
    public $message = '{{ value }} is not a valid boolean.';
}
