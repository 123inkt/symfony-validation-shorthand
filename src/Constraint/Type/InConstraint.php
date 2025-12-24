<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;

class InConstraint extends Constraint
{
    /** @var string */
    public const NOT_IN_ERROR = '790265f3-05de-47d1-ae0a-1332f5299daf';

    protected const ERROR_NAMES = [
        self::NOT_IN_ERROR => 'NOT_IN_ERROR'
    ];

    public string $message = '{{ value }} is not contained in `{{ values }}`.';

    /**
     * @inheritDoc
     * @param string[] $values
     */
    public function __construct(public array $values = [], ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }
}
