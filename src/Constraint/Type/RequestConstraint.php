<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class RequestConstraint extends Constraint
{
    public const WRONG_VALUE_TYPE           = '08937cc5-9ea6-460c-9917-d3f6ba912998';
    public const MISSING_QUERY_CONSTRAINT   = 'b62ab5ca-ee6f-4baf-bdef-ffbe14f674d6';
    public const MISSING_REQUEST_CONSTRAINT = 'c3990dad-3638-449b-9dd3-4dd42e90c52f';

    /** @var array<string, string> */
    protected static $errorNames = [
        self::WRONG_VALUE_TYPE           => 'WRONG_VALUE_TYPE',
        self::MISSING_QUERY_CONSTRAINT   => 'MISSING_QUERY_CONSTRAINT',
        self::MISSING_REQUEST_CONSTRAINT => 'MISSING_REQUEST_CONSTRAINT',
    ];

    /** @var string */
    public $wrongTypeMessage = 'Expect value to be of type Symfony\Component\HttpFoundation\Request';
    /** @var string */
    public $queryMessage = 'Request::query is not empty, but there is no constraint configured.';
    /** @var string */
    public $requestMessage = 'Request::request is not empty, but there is no constraint configured.';

    /** @var Constraint|null */
    public $query;

    /** @var Constraint|null */
    public $request;

    /**
     * @param array{?query: Constraint, ?request: Constraint}|null $options
     */
    public function __construct($options = null)
    {
        if (isset($options['query']) && $options['query'] instanceof Constraint === false) {
            throw new ConstraintDefinitionException('The option "query" is expected to be a Constraint');
        }
        if (isset($options['request']) && $options['request'] instanceof Constraint === false) {
            throw new ConstraintDefinitionException('The option "request" is expected to be a Constraint');
        }

        // make sure defaults are set
        $options            = $options ?? [];
        $options['query']   = $options['query'] ?? null;
        $options['request'] = $options['request'] ?? null;

        parent::__construct($options);
    }

    public function getRequiredOptions(): array
    {
        return ['query', 'request'];
    }
}
