<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint\Type;

use Symfony\Component\Validator\Constraint;

class TraversableConstraint extends Constraint
{
    /** @var Constraint */
    public $constraint;

    public function __construct($options = null)
    {
        if ($options instanceof Constraint) {
            $options = ['constraint' => $options];
        }
        parent::__construct($options);
    }

    public function getRequiredOptions(): array
    {
        return ['constraint'];
    }
}
