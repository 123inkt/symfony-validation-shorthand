<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraints\Collection;

class ConstraintSet
{
    /** @var Collection|null */
    private $queryConstraints;

    /** @var Collection|null */
    private $requestConstraints;

    public function getQueryConstraints(): ?Collection
    {
        return $this->queryConstraints;
    }

    public function setQueryConstraints(Collection $queryConstraints): ConstraintSet
    {
        $this->queryConstraints = $queryConstraints;
        return $this;
    }

    public function getRequestConstraints(): ?Collection
    {
        return $this->requestConstraints;
    }

    public function setRequestConstraints(Collection $requestConstraints): ConstraintSet
    {
        $this->requestConstraints = $requestConstraints;
        return $this;
    }
}
