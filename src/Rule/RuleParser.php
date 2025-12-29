<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Rule;

use Symfony\Component\Validator\Constraint;

class RuleParser
{
    /**
     * Parse a set of string rules and constraints
     *
     * @param string|Constraint|array<string|Constraint> $rules
     */
    public function parseRules(array|string|Constraint $rules): RuleList
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
     * @return Rule[]
     */
    protected function explodeExplicitRule(string $rule): array
    {
        return array_map([$this, 'parseStringRule'], explode('|', $rule));
    }

    /**
     * Parse a string based rule.
     */
    protected function parseStringRule(string $rule): Rule
    {
        $parameters = [];
        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);

            $parameters = static::parseParameters($rule, $parameter);
        }
        $rule = self::normalizeRuleName(strtolower($rule));

        return new Rule($rule, $parameters);
    }

    /**
     * Parse a parameter list.
     * @return string[]
     */
    protected static function parseParameters(string $rule, string $parameter): array
    {
        $rule = strtolower($rule);
        if ($rule === Rule::RULE_REGEX) {
            return [$parameter];
        }

        return str_getcsv($parameter, escape: "\\");
    }

    /**
     * Normalize some shorthand notations
     */
    private static function normalizeRuleName(string $name): string
    {
        return match ($name) {
            'int'   => Rule::RULE_INTEGER,
            'bool'  => Rule::RULE_BOOLEAN,
            default => $name,
        };
    }
}
