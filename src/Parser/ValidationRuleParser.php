<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use Symfony\Component\Validator\Constraint;

class ValidationRuleParser
{
    /**
     * Parse a set of string rules and constraints
     *
     * @param string|Constraint|array<string|Constraint> $rules
     * @throws RequestValidationException
     */
    public function parseRules($rules): RuleList
    {
        if (is_array($rules) === false) {
            $rules = [$rules];
        }

        $ruleSet = new RuleList();
        foreach ($rules as $rule) {
            if ($rule instanceof Constraint) {
                $ruleSet->addRule($rule);
            } else {
                $ruleSet->addAll($this->explodeExplicitRule($rule));
            }
        }

        return $ruleSet;
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
        if ($rule === Rule::RULE_REGEX) {
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
                return Rule::RULE_INTEGER;
            case 'bool':
                return Rule::RULE_BOOLEAN;
            default:
                return $name;
        }
    }
}
