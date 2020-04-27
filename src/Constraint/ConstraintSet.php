<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;

class ConstraintSet
{
    /** @var Constraint|null */
    private $queryConstraints;

    /** @var Constraint|null */
    private $requestConstraints;

    public function getQueryConstraints(): ?Constraint
    {
        return $this->queryConstraints;
    }

    public function setQueryConstraints(?Constraint $constraints): self
    {
        $this->queryConstraints = $constraints;
        return $this;
    }

    public function getRequestConstraints(): ?Constraint
    {
        return $this->requestConstraints;
    }

    public function setRequestConstraints(?Constraint $constraints): self
    {
        $this->requestConstraints = $constraints;
        return $this;
    }
}
