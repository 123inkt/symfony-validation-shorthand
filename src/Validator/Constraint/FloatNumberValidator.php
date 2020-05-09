<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FloatNumberValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof IntegerNumber === false) {
            throw new UnexpectedTypeException($constraint, IntegerNumber::class);
        }

        if ($value === null && $constraint->allowNull) {
            return;
        }

        if (is_float($value)) {
            return;
        }

        // value should be either float or string
        if (is_string($value) === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode($constraint::INVALID_VALUE_TYPE)
                ->addViolation();
        }

        // value can't be cast to float
        if (((string)(float)$value) !== $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode($constraint::INVALID_NUMBER_ERROR)
                ->addViolation();
        }
    }
}
