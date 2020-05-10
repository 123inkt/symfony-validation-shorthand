<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use Symfony\Component\Validator\Constraint;

class RuleListMapper
{
    /** @var array<string, string|Constraint|array<string|Constraint>> */
    private $rules;

    /** @var ValidationRuleParser */
    private $parser;

    /**
     * @param array<string, string|Constraint|array<string|Constraint>> $rules
     */
    public function __construct(array $rules, ValidationRuleParser $parser = null)
    {
        $this->rules  = $rules;
        $this->parser = $parser ?? new ValidationRuleParser();
    }

    /**
     * @throws RequestValidationException
     */
    public function createRuleListMap(): RuleListMap
    {
        $map = new RuleListMap();
        foreach ($this->rules as $key => $rules) {
            $map->set($key, $this->parser->parseRules($rules));
        }

        return $map;
    }
}
