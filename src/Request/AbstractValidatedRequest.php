<?php

namespace PrinsFrank\SymfonyRequestValidation\Request;

use PrinsFrank\SymfonyRequestValidation\Response\InvalidRequestResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    protected $request;

    /** @var ValidatorInterface  */
    protected $validator;

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function queryRules(): array;

    /**
     * Get all the constraints for the current request params
     */
    abstract protected function requestRules(): array;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = Validation::createValidator();

        $this->validate();
    }

    /**
     * @return InvalidRequestResponse|void
     */
    protected function validate()
    {
        $violationList = new ConstraintViolationList();

        foreach($this->queryRules() as $property => $requestRule) {
            $violationList->addAll($this->validator->validate($this->request->query->get($property), $requestRule));
        }

        foreach($this->requestRules() as $property => $requestRule) {
            $violationList->addAll($this->validator->validate($this->request->request->get($property), $requestRule));
        }

        if ($violationList->count() > 0) {
            new InvalidRequestResponse($violationList);
        }
    }
}