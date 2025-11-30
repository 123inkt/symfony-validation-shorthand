<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Rule;

use Countable;
use Symfony\Component\Validator\Constraint;

class RuleList implements Countable
{
    /** @var array<Rule|Constraint> */
    private array $rules = [];

    /**
     * Rule type lookup index
     * @var array<string, true>
     */
    private array $ruleTypes = [];

    /**
     * @param string|string[] $names
     */
    public function hasRule(array|string $names): bool
    {
        $names = is_array($names) ? $names : [$names];
        foreach ($names as $name) {
            if (isset($this->ruleTypes[$name]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool false if all rules are of type Constraint. true otherwise.
     */
    public function hasRules(): bool
    {
        return count($this->ruleTypes) > 0;
    }

    /**
     * @return array<Rule|Constraint>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(Rule|Constraint $rule): self
    {
        $this->rules[] = $rule;
        if ($rule instanceof Rule) {
            $this->ruleTypes[$rule->getName()] = true;
        }

        return $this;
    }

    /**
     * @param array<Rule|Constraint> $rules
     */
    public function addAll(array $rules): self
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    public function count(): int
    {
        return count($this->rules);
    }
}
