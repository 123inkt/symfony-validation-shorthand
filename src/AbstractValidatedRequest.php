<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation;

use PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintSetFactory;
use PrinsFrank\SymfonyRequestValidation\Exception\RequestValidationException;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\ValidationRuleParseException;
use PrinsFrank\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatedRequest
{
    /** @var RequestValidator */
    protected $validator;

    /**
     * @throws RequestValidationException|ValidationRuleParseException
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            return;
        }

        $this->validator = new RequestValidator($validator);
        $this->validate($request);
    }

    /**
     * Get all the constraints for the current query params
     */
    abstract protected function getRuleSet(): ValidationRules;

    /**
     * @throws RequestValidationException|ValidationRuleParseException
     */
    protected function validate(Request $request): void
    {
        $constraintSet = ConstraintSetFactory::createFromRuleset($this->getRuleSet());
        $violationList = $this->validator->validate($request, $constraintSet);
        if ($violationList->count() > 0) {
            throw new RequestValidationException((string)$violationList);
        }
    }
}
