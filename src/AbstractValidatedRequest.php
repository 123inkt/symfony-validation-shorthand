<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    protected $request;

    /** @var bool */
    protected $isValid;

    /**
     * @throws RequestValidationException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new RequestValidationException('Request is missing, unable to validate');
        }

        $this->request = $request;
        $this->isValid = $this->validate($request, new RequestValidator($validator));
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
    abstract protected function getValidationRules(Request $request): ValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling
     *
     * @param ConstraintViolationList<ConstraintViolationInterface> $violationList
     * @throws RequestValidationException
     */
    protected function handleViolations(ConstraintViolationList $violationList): void
    {
        throw new RequestValidationException((string)$violationList);
    }

    /**
     * @throws RequestValidationException
     */
    protected function validate(Request $request, RequestValidator $validator): bool
    {
        $violationList = $validator->validate($request, $this->getValidationRules($request));
        if (count($violationList) > 0) {
            $this->handleViolations($violationList);
            return false;
        }

        return true;
    }
}
