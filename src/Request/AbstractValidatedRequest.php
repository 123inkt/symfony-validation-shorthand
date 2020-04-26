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
    /** @var Request */
    protected $request;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @throws RequestValidationException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;

        // TODO: don't invoke validate in constructor
        $this->validate();
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getConstraints(): ConstraintSet;

    /**
     * @return InvalidRequestResponse|void
     * @throws RequestValidationException
     */
    protected function validate()
    {
        $violationList = new ConstraintViolationList();
        $constraints = $this->getConstraints();

        foreach ($constraints->getQueryConstraints() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($this->request->query->get($property), $constraints));
        }

        foreach ($constraints->getRequestConstraints() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($this->request->request->get($property), $constraints));
        }

        if ($violationList->count() > 0) {
            throw new RequestValidationException((string)$violationList);
        }
    }
}
