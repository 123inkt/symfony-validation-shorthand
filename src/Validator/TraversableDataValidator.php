<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Traversable;

/**
 * This validator will validate each entry received from a traversable array. Unlike ArrayValidator and RequestValidator where
 * the whole set is defined by the constraint. The constraint for this validator defines 1 iteration.
 */
class TraversableDataValidator extends AbstractValidator
{
    public function validate(Traversable $data): ConstraintViolationListInterface
    {
        return $this->validator->validate($data, $this->constraint);
    }
}
