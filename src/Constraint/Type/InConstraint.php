<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;

class InConstraint extends Constraint
{
    public const NOT_IN_ERROR = '790265f3-05de-47d1-ae0a-1332f5299daf';

    /** @var mixed */
    protected static $errorNames = [
        self::NOT_IN_ERROR => 'NOT_IN_ERROR'
    ];

    /** @var string */
    public $message = '{{ value }} is not contained in `{{ values }}`.';

    /** @var string[] */
    public $values;

    /**
     * @inheritDoc
     */
    public function getRequiredOptions(): array
    {
        return ['values'];
    }
}
