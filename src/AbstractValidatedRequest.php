<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    protected $request;

    /** @var bool */
    protected $isValid;

    /**
     * @throws RequestValidationException
     * @throws Utility\InvalidArrayPathException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new RequestValidationException('Request is missing, unable to validate');
        }

        $this->request = $request;
        $rules         = $this->getValidationRules($request);
        $dataValidator = (new DataValidatorFactory($validator))->createRequestValidator($rules->getQueryRules(), $rules->getRequestRules());
        $this->isValid = $this->validate($request, $dataValidator);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getValidationRules(Request $request): RequestValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling
     *
     * @param ConstraintViolationListInterface $violationList
     * @throws RequestValidationException
     */
    protected function handleViolations(ConstraintViolationListInterface $violationList): void
    {
        throw new RequestValidationException((string)$violationList);
    }

    /**
     * @throws RequestValidationException
     */
    protected function validate(Request $request, RequestValidator $validator): bool
    {
        $violationList = $validator->validate($request);
        if (count($violationList) > 0) {
            $this->handleViolations($violationList);
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }
}
