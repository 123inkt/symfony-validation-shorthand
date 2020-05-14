<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Mock;

use DigitalRevolution\SymfonyValidationShorthand\AbstractValidatedRequest;
use DigitalRevolution\SymfonyValidationShorthand\RequestValidationRules;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MockValidatedRequest extends AbstractValidatedRequest
{
    /** @var RequestValidationRules|null */
    private $rules;

    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, RequestValidationRules $rules = null)
    {
        $this->rules = $rules;
        parent::__construct($requestStack, $validator);
    }

    /**
     * @inheritDoc
     */
    protected function getValidationRules(): RequestValidationRules
    {
        if ($this->rules === null) {
            throw new RuntimeException('ValidationRules not set');
        }
        return $this->rules;
    }
}
