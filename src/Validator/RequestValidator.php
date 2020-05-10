<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidator extends AbstractValidator
{
    public function validate(Request $request): ConstraintViolationListInterface
    {
        return $this->validator->validate($request, $this->constraint);
    }
}
