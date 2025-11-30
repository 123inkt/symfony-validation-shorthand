<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class InConstraintValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($constraint instanceof InConstraint === false) {
            throw new UnexpectedTypeException($constraint, InConstraint::class);
        }

        if ($value === null || is_scalar($value) === false) {
            return;
        }

        // value is not contained within the allowed values
        if (in_array((string)$value, $constraint->values, true) === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ values }}', implode(',', $constraint->values))
                ->setCode($constraint::NOT_IN_ERROR)
                ->addViolation();
        }
    }
}
