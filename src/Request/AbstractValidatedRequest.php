<?php

namespace PrinsFrank\SymfonyRequestValidation\Request;

use PrinsFrank\SymfonyRequestValidation\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    private $request;

    /** @var ValidatorInterface  */
    private $validator;

    abstract protected function rules(): Collection;

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
     * @return bool
     * @throws RequestValidationException
     */
    protected function validate(): bool
    {
        $violationList = $this->validator->validate(
            $this->request->request,
            $this->rules()
        );

        if ($violationList->count() > 0) {
            throw new RequestValidationException("Invalid Request");
        }

        return true;
    }
}