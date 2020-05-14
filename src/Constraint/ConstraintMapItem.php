<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint;

use Symfony\Component\Validator\Constraint;

class ConstraintMapItem
{
    /** @var Constraint[] */
    private $constraints;

    /** @var bool */
    private $required;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(array $constraints, bool $required = false)
    {
        $this->constraints = $constraints;
        $this->required    = $required;
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
