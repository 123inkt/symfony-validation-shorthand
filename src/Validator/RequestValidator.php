<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Validator;

use PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Request $request, ConstraintSet $constraintSet): ConstraintViolationList
    {
        $violations = new ConstraintViolationList();

        if ($constraintSet->getQueryConstraints() !== null) {
            $violations->addAll($this->validator->validate($request->query->all(), $constraintSet->getQueryConstraints()));
        }

        if ($constraintSet->getRequestConstraints() !== null) {
            $violations->addAll($this->validator->validate($request->request->all(), $constraintSet->getRequestConstraints()));
        }

        return $violations;
    }
}
