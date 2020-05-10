<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use ArrayAccess;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Traversable;

class ArrayValidator extends AbstractValidator
{
    /**
     * @param array<mixed>|Traversable&ArrayAccess $data
     */
    public function validate(array $data): ConstraintViolationListInterface
    {
        return $this->validator->validate($data, $this->constraint);
    }
}
