<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use Countable;
use Symfony\Component\Validator\Constraint;

class RuleSet implements Countable
{
    /** @var array<Rule|Constraint> */
    private $rules = [];

    /** @var array<string, true> */
    private $ruleTypes = [];

    public function hasRule(string $name): bool
    {
        return isset($this->ruleTypes[$name]);
    }

    /**
     * @return array<Rule|Constraint>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @var Rule|Constraint $rule
     */
    public function addRule($rule): self
    {
        $this->rules[] = $rule;
        if ($rule instanceof Rule) {
            $this->ruleTypes[$rule->getName()] = true;
        }

        return $this;
    }

    /**
     * @var array<Rule|Constraint> $rules
     */
    public function addAll(array $rules): self
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    public function count()
    {
        return count($this->rules);
    }
}
