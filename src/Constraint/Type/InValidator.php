<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class InValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof In === false) {
            throw new UnexpectedTypeException($constraint, In::class);
        }

        if ($value === null) {
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
