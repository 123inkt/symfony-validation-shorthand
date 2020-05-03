<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

class ValidationRuleParser
{
    /** @var ConstraintResolver */
    private $resolver;

    public function __construct(ConstraintResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param array<mixed, string|Constraint|array<string|Constraint>> $fieldRules
     * @throws RequestValidationException
     */
    public function parse(array $fieldRules): Collection
    {
        $result = [];
        foreach ($fieldRules as $field => $rules) {
            if (is_string($field) === false) {
                throw new RequestValidationException('Field names should be string. Field type is: ' . gettype($field));
            }
            $result[$field] = $this->parseRules(is_array($rules) ? $rules : [$rules]);
        }

        return new Collection(['fields' => $result]);
    }

    /**
     * Parse a set of string rules and constraints
     *
     * @param array<string|Constraint> $rules
     * @throws RequestValidationException
     */
    protected function parseRules(array $rules): Constraint
    {
        $ruleSet = new RuleSet();
        foreach ($rules as $rule) {
            if ($rule instanceof Constraint) {
                $ruleSet->addRule($rule);
            } else {
                $ruleSet->addAll($this->explodeExplicitRule($rule));
            }
        }

        return $this->resolver->resolveRuleSet($ruleSet);
    }

    /**
     * Explode a string rule
     *
     * @param mixed $rule
     * @return Rule[]
     * @throws RequestValidationException
     */
    protected function explodeExplicitRule($rule): array
    {
        if (is_string($rule)) {
            return array_map([$this, 'parseStringRule'], explode('|', $rule));
        }
        throw new RequestValidationException('Invalid rule definition type. Expecting string or Symfony\Component\Validator\Constraint');
    }

    /**
     * Parse a string based rule.
     */
    protected function parseStringRule(string $rule): Rule
    {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $parameter] = explode(':', $rule, 2);

            $parameters = static::parseParameters($rule, $parameter);
        }
        $rule = self::normalizeRuleName(strtolower($rule));
        return new Rule($rule, $parameters);
    }

    /**
     * Parse a parameter list.
     *
     * @return string[]
     */
    protected static function parseParameters(string $rule, string $parameter): array
    {
        $rule = strtolower($rule);
        if ($rule === 'regex') {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * Normalize some shorthand notations
     */
    private static function normalizeRuleName(string $name): string
    {
        switch ($name) {
            case 'int':
                return 'integer';
            case 'bool':
                return 'boolean';
            default:
                return $name;
        }
    }
}
