<?php

namespace PrinsFrank\SymfonyRequestValidation\Rule;


use Symfony\Component\Validator\Constraint;

class RuleSet
{
    /** @var array<string, array<string|Constraint>> */
    private $queryRules = [];

    /** @var array<string, array<string|Constraint>> */
    private $requestRules = [];

    /**
     * @return array<string, array<string|Constraint>>
     */
    public function getQueryRules(): array
    {
        return $this->queryRules;
    }

    /**
     * @param array<string, array<string|Constraint>> $queryRules
     */
    public function setQueryRules(array $queryRules): self
    {
        $this->queryRules = $queryRules;
        return $this;
    }

    /**
     * @param string|Constraint $rule
     */
    public function addQueryConstraints(string $field, $rule): self
    {
        if (isset($this->queryRules[$field]) === false) {
            $this->queryRules[$field] = [];
        }

        $this->queryRules[$field] = $rule;
        return $this;
    }

    /**
     * @return array<string, array<string|Constraint>>
     */
    public function getRequestRules(): array
    {
        return $this->requestRules;
    }

    /**
     * @param array<string, array<string|Constraint>> $requestRules
     */
    public function setRequestRules(array $requestRules): self
    {
        $this->requestRules = $requestRules;
        return $this;
    }

    /**
     * @param string|Constraint $rule
     */
    public function addRequestRule(string $field, $rule): self
    {
        if (isset($this->requestRules[$field]) === false) {
            $this->requestRules[$field] = [];
        }

        $this->requestRules[$field] = $rule;
        return $this;
    }
}
