<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidator
{
    /** @var Constraint */
    protected $constraint;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(Constraint $constraint, ValidatorInterface $validator)
    {
        $this->constraint = $constraint;
        $this->validator  = $validator;
    }
}
