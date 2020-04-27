<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Tests\Mock;

use PrinsFrank\SymfonyRequestValidation\AbstractValidatedRequest;
use PrinsFrank\SymfonyRequestValidation\ValidationRules;
use PrinsFrank\SymfonyRequestValidation\Validator\RequestValidator;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MockValidatedRequest extends AbstractValidatedRequest
{
    /** @var ValidationRules|null */
    private $rules;

    /** @var bool */
    private $validated = false;

    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, ValidationRules $rules = null)
    {
        $this->rules = $rules;
        parent::__construct($requestStack, $validator);
    }


    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @inheritDoc
     */
    protected function getValidationRules(): ValidationRules
    {
        if ($this->rules === null) {
            throw new RuntimeException('ValidationRules not set');
        }
        return $this->rules;
    }

    protected function validate(Request $request, RequestValidator $validator): void
    {
        parent::validate($request, $this->requestValidator ?? $validator);
        $this->validated = true;
    }
}
