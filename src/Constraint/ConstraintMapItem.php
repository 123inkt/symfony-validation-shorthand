<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint;

use Symfony\Component\Validator\Constraint;

class ConstraintMapItem
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(private array $constraints, private bool $required = false)
    {
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
