<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint\Type;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequestConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof RequestConstraint === false) {
            throw new UnexpectedTypeException($constraint, RequestConstraint::class);
        }

        if ($value === null) {
            return;
        }

        if ($value instanceof Request === false) {
            throw new InvalidArgumentException('Expecting value to be of type Symfony\Component\HttpFoundation\Request');
        }

        $context = $this->context;

        if ($constraint->queryConstraint !== null) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[query]')
                ->validate($value->query->all(), $constraint->queryConstraint);
        } elseif (count($value->query) > 0) {
            $context->buildViolation($constraint->missingQueryConstraintMessage)
                ->atPath('[query]')
                ->setCode($constraint::MISSING_QUERY_CONSTRAINT)
                ->addViolation();
        }

        if ($constraint->requestConstraint !== null) {
            $context->getValidator()
                ->inContext($context)
                ->atPath('[request]')
                ->validate($value->query->all(), $constraint->requestConstraint);
        } elseif (count($value->query) > 0) {
            $context->buildViolation($constraint->missingRequestConstraintMessage)
                ->atPath('[request]')
                ->setCode($constraint::MISSING_REQUEST_CONSTRAINT)
                ->addViolation();
        }
    }
}
