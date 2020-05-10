<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint\Type;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Traversable;

class TraversableConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof TraversableConstraint === false) {
            throw new UnexpectedTypeException($constraint, TraversableConstraint::class);
        }

        if ($value === null) {
            return;
        }

        if ($value instanceof Traversable === false) {
            throw new InvalidArgumentException('Expecting value to be of type Symfony\Component\HttpFoundation\Request');
        }

        // safe reference to context
        $context = $this->context;

        foreach ($value as $key => $row) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[' . $key . ']')
                ->validate($row, $constraint->constraint);
        }
    }
}
