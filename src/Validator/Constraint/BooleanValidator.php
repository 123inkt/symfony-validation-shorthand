<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BooleanValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof Boolean === false) {
            throw new UnexpectedTypeException($constraint, IntegerNumber::class);
        }

        if ($value === null || is_bool($value)) {
            return;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE | FILTER_REQUIRE_SCALAR]);
        if ($filtered === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode($constraint::INVALID_BOOLEAN_ERROR)
                ->addViolation();
        }
    }
}
