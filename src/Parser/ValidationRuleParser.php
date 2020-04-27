<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class ValidationRuleParser
{
    /** @var array<string, array<string|Constraint> */
    private $data;

    /**
     * @param array<string, array<string|Constraint> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array<string, RuleSet>
     */
    public function parse(): array
    {
        $data = [];
        foreach ($this->data as $field => $rules) {
            if (is_string($field) === false) {
                throw new InvalidArgumentException('Invalid field names should be string. Field type is: ' . gettype($field));
            }
            $data[$field] = $this->parseRules(is_array($rules) ? $rules : [$rules]);
        }

        return $data;
    }

    protected function parseRules(array $rules): RuleSet
    {
        $ruleSet = new RuleSet();
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
     */
    protected function explodeExplicitRule($rule): array
    {
        if (is_string($rule)) {
            return array_map([$this, 'parseStringRule'], explode('|', $rule));
        }
        throw new InvalidArgumentException('Invalid rule definition type. Expecting string or constraint');
    }

    /**
     * Parse a string based rule.
     */
    protected function parseStringRule(string $rule): Rule
    {
        $parameters = [];

        // The format for specifying validation rules and parameters follows an
        // easy {rule}:{parameters} formatting convention. For instance the
        // rule "Max:3" states that the value may only be three letters.
        if (strpos($rule, ':') !== false) {
            [$rule, $parameter] = explode(':', $rule, 2);

            $parameters = static::parseParameters($rule, $parameter);
        }

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
        if (in_array($rule, ['regex', 'not_regex', 'notregex'], true)) {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }
}
