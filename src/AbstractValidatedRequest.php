<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation;

use PrinsFrank\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /**
     * @throws RequestValidationException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            return;
        }

        $this->validate($request, new RequestValidator($validator));
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getValidationRules(): ValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling
     *
     * @throws RequestValidationException
     */
    protected function handleViolations(ConstraintViolationList $violationList): void
    {
        throw new RequestValidationException((string)$violationList);
    }

    /**
     * @throws RequestValidationException
     */
    protected function validate(Request $request, RequestValidator $validator): void
    {
        $violationList = $validator->validate($request, $this->getValidationRules());
        if (count($violationList) > 0) {
            $this->handleViolations($violationList);
        }
    }
}
