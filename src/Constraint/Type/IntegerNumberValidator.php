<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IntegerNumberValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($constraint instanceof IntegerNumber === false) {
            throw new UnexpectedTypeException($constraint, IntegerNumber::class);
        }

        if ($value === null || is_int($value)) {
            return;
        }

        // value should be either float or string
        if (is_string($value) === false && is_float($value) === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode($constraint::INVALID_VALUE_TYPE)
                ->addViolation();
            return;
        }

        // value can't be cast to int
        if (@((string)(int)$value) !== (string)$value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode($constraint::INVALID_NUMBER_ERROR)
                ->addViolation();
        }
    }
}
