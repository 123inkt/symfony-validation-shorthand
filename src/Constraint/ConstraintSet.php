<?php

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

class ConstraintSet
{
    /** @var array<string, Collection[]> */
    private $queryConstraints = [];

    /** @var array<string, Collection[]> */
    private $requestConstraints = [];

    /**
     * @return array<string, Collection[]>
     */
    public function getQueryConstraints(): array
    {
        return $this->queryConstraints;
    }

    /**
     * @param array<string, Collection[]> $queryConstraints
     */
    public function setQueryConstraints(array $queryConstraints): self
    {
        $this->queryConstraints = $queryConstraints;
        return $this;
    }

    /**
     * @return array<string, Collection[]>
     */
    public function getRequestConstraints(): array
    {
        return $this->requestConstraints;
    }

    /**
     * @param array<string, Collection[]> $requestConstraints
     */
    public function setRequestConstraints(array $requestConstraints): self
    {
        $this->requestConstraints = $requestConstraints;
        return $this;
    }
}
