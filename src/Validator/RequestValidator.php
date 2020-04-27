<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Validator;

use PrinsFrank\SymfonyRequestValidation\ValidationRules;
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

    public function validate(Request $request, ValidationRules $rules): ConstraintViolationList
    {
        $violations = new ConstraintViolationList();

        if ($rules->getQueryRules() !== null) {
            $violations->addAll($this->validator->validate($request->query->all(), $rules->getQueryRules()));
        }

        if ($rules->getRequestRules() !== null) {
            $violations->addAll($this->validator->validate($request->request->all(), $rules->getRequestRules()));
        }

        return $violations;
    }
}
