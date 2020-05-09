<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Validator\Constraint\RecursiveCollection;
use Symfony\Component\Validator\Constraint;

class RecursiveConstraintResolver
{
    /** @var ConstraintResolver */
    private $resolver;

    /** @var ValidationRuleParser */
    private $parser;

    public function __construct()
    {
        $this->parser   = new ValidationRuleParser();
        $this->resolver = new ConstraintResolver();
    }

    /**
     * @param mixed $rules
     * @throws RequestValidationException
     */
    public function resolve($rules): Constraint
    {
        $constraints = [];
        foreach ($rules as $path => $rule) {
            $constraints[$path] = $this->resolver->resolveRuleSet($this->parser->parseRules($rule));
        }

        return new RecursiveCollection($constraints);
    }
}
