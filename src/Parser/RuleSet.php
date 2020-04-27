<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use Countable;
use Symfony\Component\Validator\Constraint;

class RuleSet implements Countable
{
    /** @var array<Rule|Constraint> */
    private $rules = [];

    public function hasRule(string $name): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof Rule && $rule->getName() === $name) {
                return true;
            }
        }

        return false;
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
        return $this;
    }

    /**
     * @var array<Rule|Constraint> $rules
     */
    public function addAll(array $rules): self
    {
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    /**
     * @var array<Rule|Constraint> $rules
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    public function count()
    {
        return count($this->rules);
    }
}
