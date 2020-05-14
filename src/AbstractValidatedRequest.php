<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var Request */
    protected $request;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var Constraint */
    protected $constraint;

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

        $this->request    = $request;
        $this->validator  = $validator;
        $this->constraint = (new ConstraintFactory())->createRequestConstraint($this->getValidationRules());
        $this->isValid    = $this->validate();
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
    abstract protected function getValidationRules(): RequestValidationRules;

    /**
     * Called when there are one or more violations. Defaults to throwing RequestValidationException. Overwrite
     * to add your own handling
     *
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
     * @throws RequestValidationException
     */
    protected function handleViolations(ConstraintViolationListInterface $violationList): void
    {
        $messages = [];
        foreach ($violationList as $violation) {
            $messages[] = $violation->getMessage();
        }

        throw new RequestValidationException(implode("\n", $messages));
    }

    /**
     * @throws RequestValidationException
     */
    protected function validate(): bool
    {
        $violationList = $this->validator->validate($this->request, $this->constraint);
        if (count($violationList) > 0) {
            $this->handleViolations($violationList);
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }
}
