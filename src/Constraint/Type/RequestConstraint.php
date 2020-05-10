<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class RequestConstraint extends Constraint
{
    public const MISSING_QUERY_CONSTRAINT   = 'b62ab5ca-ee6f-4baf-bdef-ffbe14f674d6';
    public const MISSING_REQUEST_CONSTRAINT = 'c3990dad-3638-449b-9dd3-4dd42e90c52f';

    protected static $errorNames = [
        self::MISSING_QUERY_CONSTRAINT   => 'MISSING_QUERY_CONSTRAINT',
        self::MISSING_REQUEST_CONSTRAINT => 'MISSING_REQUEST_CONSTRAINT',
    ];

    public $missingQueryConstraintMessage   = 'Request::query is not empty, but there is no constraint configured.';
    public $missingRequestConstraintMessage = 'Request::request is not empty, but there is no constraint configured.';

    /** @var Constraint|null */
    public $queryConstraint;

    /** @var Constraint|null */
    public $requestConstraint;

    public function __construct($options = null)
    {
        if (isset($options['queryConstraint']) && $options['queryConstraint'] instanceof Constraint === false) {
            throw new ConstraintDefinitionException('The option "queryConstraint" is expected to be a Constraint');
        }
        if (isset($options['requestConstraint']) && $options['requestConstraint'] instanceof Constraint === false) {
            throw new ConstraintDefinitionException('The option "requestConstraint" is expected to be a Constraint');
        }

        parent::__construct($options);
    }

    public function getRequiredOptions(): array
    {
        return ['queryConstraint', 'requestConstraint'];
    }
}
