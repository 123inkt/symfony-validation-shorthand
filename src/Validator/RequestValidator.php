<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator;

use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    /** @var ValidationRuleParser */
    private $parser;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator, ValidationRuleParser $parser)
    {
        $this->parser    = $parser;
        $this->validator = $validator;
    }

    /**
     * @return ConstraintViolationList<ConstraintViolationInterface>
     * @throws RequestValidationException
     */
    public function validate(Request $request, ValidationRules $rules): ConstraintViolationList
    {
        $violations   = new ConstraintViolationList();
        $queryRules   = $rules->getQueryRules();
        $requestRules = $rules->getRequestRules();

        if ($queryRules instanceof Constraint) {
            $violations->addAll($this->validator->validate($request->query->all(), $queryRules));
        } elseif ($queryRules !== null) {
            $violations->addAll($this->validator->validate($request->query->all(), $this->parser->parse($queryRules)));
        }

        if ($requestRules instanceof Constraint) {
            $violations->addAll($this->validator->validate($request->request->all(), $requestRules));
        } elseif ($requestRules !== null) {
            $violations->addAll($this->validator->validate($request->request->all(), $this->parser->parse($requestRules)));
        }

        return $violations;
    }
}
