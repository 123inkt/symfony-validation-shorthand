<?php

namespace PrinsFrank\SymfonyRequestValidation\Request;

use PrinsFrank\SymfonyRequestValidation\Response\InvalidRequestResponse;
use PrinsFrank\SymfonyRequestValidation\Response\UnauthorizedRequestResponse;
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
     * Get all the constraints for the current request
     */
    abstract protected function rules(): array;

    /**
     * Determine if the user is authorized to make this request
     */
    abstract protected function authorize(): bool;

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
        if(!$this->authorize()){
            new UnauthorizedRequestResponse();
        }

        $violationList = new ConstraintViolationList();
        foreach($this->rules() as $property => $rules) {
            $violationList->addAll($this->validator->validate(
                $this->request->get($property),
                $rules
            ));
        }

        if ($violationList->count() > 0) {
            new InvalidRequestResponse($violationList);
        }
    }
}