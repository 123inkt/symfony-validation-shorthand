<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Utility\ArrayAssignException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @throws RequestValidationException|ArrayAssignException
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

        $constraint = $this->createAllConstraint($ruleSets);
        if ($constraint === null) {
            $constraint = new Collection($ruleSets);
        }

        return $constraint;
    }

    /**
     * Assert\All should be used instead of Assert\Collection when:
     * - only one rule is defined within rules
     * - the key of the rule is either '*' or '+'  (with/without '?' optional indicator)
     */
    private function createAllConstraint(array $rules): ?Constraint
    {
        if (count($rules) !== 1) {
            return null;
        }

        $key      = key($rules);
        $optional = false;
        if (str_ends_with($key, '?')) {
            $optional = true;
            $key      = substr($key, -1);
        }

        switch ($key) {
            case '*':
                $constraints = [new Assert\Type('array'), new Assert\All($rules[$key])];
                return $optional ? new Assert\Optional($constraints) : new Assert\Required($constraints);
            case '+':
                $constraints = [new Assert\Type('array'), new Assert\Count(['min' => 1]), new Assert\All($rules[$key])];
                return $optional ? new Assert\Optional($constraints) : new Assert\Required($constraints);
            default:
                return null;
        }
    }
}
