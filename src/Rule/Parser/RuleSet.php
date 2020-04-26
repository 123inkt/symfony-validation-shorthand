<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Rule\Parser;

class RuleSet
{
    /** @var Rule[] */
    private $rules = [];

    /** @var bool */
    private $required = false;

    public function addRule(Rule $rule): self
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }
}
