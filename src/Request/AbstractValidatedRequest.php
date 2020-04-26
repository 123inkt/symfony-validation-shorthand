<?php

namespace PrinsFrank\SymfonyRequestValidation\Request;

use PrinsFrank\SymfonyRequestValidation\Exception\RequestValidationException;
use PrinsFrank\SymfonyRequestValidation\Rule\RuleSet;
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
    abstract protected function getRuleSet(): RuleSet;

    /**
     * @throws RequestValidationException
     */
    protected function validate(Request $request)
    {
        $ruleset = $this->getRuleSet();
        $violationList = new ConstraintViolationList();

        foreach ($constraints->getQueryRules() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($request->query->get($property), $constraints));
        }

        foreach ($constraints->getRequestRules() as $property => $constraints) {
            $violationList->addAll($this->validator->validate($request->request->get($property), $constraints));
        }

        if ($violationList->count() > 0) {
            throw new RequestValidationException((string)$violationList);
        }
    }
}
