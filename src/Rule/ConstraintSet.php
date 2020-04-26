<?php

namespace PrinsFrank\SymfonyRequestValidation\Rule;


use Symfony\Component\Validator\Constraint;

class ConstraintSet
{
    /** @var Constraint[] */
    private $queryConstraints = [];

    /** @var Constraint[] */
    private $requestConstraints = [];

    /**
     * @return Constraint[]
     */
    public function getQueryConstraints(): array
    {
        return $this->queryConstraints;
    }

    /**
     * @param Constraint[] $queryConstraints
     */
    public function setQueryConstraints(array $queryConstraints): self
    {
        $this->queryConstraints = $queryConstraints;
        return $this;
    }

    public function addQueryConstraints(Constraint $constraint): self
    {
        $this->queryConstraints[] = $constraint;
        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function getRequestConstraints(): array
    {
        return $this->requestConstraints;
    }

    /**
     * @param Constraint[] $requestConstraints
     */
    public function setRequestConstraints(array $requestConstraints): self
    {
        $this->requestConstraints = $requestConstraints;
        return $this;
    }

    public function addRequestConstraints(Constraint $constraint): self
    {
        $this->requestConstraints[] = $constraint;
        return $this;
    }
}
