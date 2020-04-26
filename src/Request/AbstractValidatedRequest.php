<?php

namespace PrinsFrank\SymfonyRequestValidation\Request;

use PrinsFrank\SymfonyRequestValidation\Exception\RequestValidationException;
use PrinsFrank\SymfonyRequestValidation\Response\InvalidRequestResponse;
use PrinsFrank\SymfonyRequestValidation\Rule\ConstraintSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @throws RequestValidationException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        $this->validator = $validator;

        if ($request !== null) {
            $this->validate($request);
        }
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getConstraints(): ConstraintSet;

    /**
     * @throws RequestValidationException
     */
    protected function validate(Request $request)
    {
        $violationList = new ConstraintViolationList();
        $constraints = $this->getConstraints();

        foreach ($constraints->getQueryConstraints() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($request->query->get($property), $constraints));
        }

        foreach ($constraints->getRequestConstraints() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($request->request->get($property), $constraints));
        }

        if ($violationList->count() > 0) {
            throw new RequestValidationException((string)$violationList);
        }
    }
}
