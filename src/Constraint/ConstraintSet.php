<?php

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;

class ConstraintSet
{
    /** @var array<string, Constraint[]> */
    private $queryConstraints = [];

    /** @var array<string, Constraint[]> */
    private $requestConstraints = [];

    /**
     * @return array<string, Constraint[]>
     */
    public function getQueryConstraints(): array
    {
        return $this->queryConstraints;
    }

    /**
     * @param array<string, Constraint[]> $queryConstraints
     */
    public function setQueryConstraints(array $queryConstraints): self
    {
        $this->queryConstraints = $queryConstraints;
        return $this;
    }

    /**
     * @return array<string, Constraint[]>
     */
    public function getRequestConstraints(): array
    {
        return $this->requestConstraints;
    }

    /**
     * @param array<string, Constraint[]> $requestConstraints
     */
    public function setRequestConstraints(array $requestConstraints): self
    {
        $this->requestConstraints = $requestConstraints;
        return $this;
    }
}
