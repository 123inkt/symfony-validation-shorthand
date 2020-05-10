<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

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
     * @throws RequestValidationException|InvalidArrayPathException
     */
    public function resolve($rules): Constraint
    {
        $constraints = [];
        foreach ($rules as $path => $rule) {
            Arrays::assignToPath($constraints, explode('.', $path), $this->parser->parseRules($rule));
        }

        return $this->createConstraintCollection($constraints);
    }

    /**
     *
     * @throws RequestValidationException
     */
    private function createConstraintCollection(array $ruleSets)
    {
        // array contains arrays, recursively resolve
        foreach ($ruleSets as $key => $set) {
            if ($set instanceof RuleSet) {
                $ruleSets[$key] = $this->resolver->resolveRuleSet($set);
            } else {
                $ruleSets[$key] = $this->createConstraintCollection($set);
            }
        }

        // create Assert\All constraint if needed.
        $constraint = ConstraintFactory::createAllConstraint($ruleSets);
        if ($constraint === null) {
            $constraint = new Collection($ruleSets);
        }

        return $constraint;
    }
}
