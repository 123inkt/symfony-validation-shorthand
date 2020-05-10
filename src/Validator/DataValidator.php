<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Traversable;

class DataValidator extends AbstractValidator
{
    /**
     * @param array<mixed>|Traversable $data
     */
    public function validate($data): ConstraintViolationListInterface
    {
        return $this->validator->validate($data, $this->constraint);
    }
}
