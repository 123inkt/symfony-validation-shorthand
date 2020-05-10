<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Builder;

use DigitalRevolution\SymfonyRequestValidation\Parser\RuleListMap;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use Symfony\Component\Validator\Constraint;

class RuleListMapBuilder
{
    /** @var ValidationRuleParser */
    private $parser;

    public function __construct(ValidationRuleParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     */
    public function build(array $ruleDefinitions): RuleListMap
    {
        $map = new RuleListMap();
        foreach ($ruleDefinitions as $key => $rules) {
            $map->set($key, $this->parser->parseRules($rules));
        }

        return $map;
    }
}
