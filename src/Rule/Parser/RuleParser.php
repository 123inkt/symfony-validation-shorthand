<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Rule\Parser;

/**
 * BNF:
 *
 * rules     ::= <rule> | <rule> "," <rule>
 * rule      ::= <name> | <name> ":" <argument>
 * name      ::= <alphanum>
 * argument  ::= <alphanum>
 * alphanum  ::= [a-zA-Z0-9_]
 */
class RuleParser
{
    /** @var StringReader */
    private $reader;

    public function __construct(StringReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @throws ValidationRuleParseException
     */
    public function parseRules(): RuleSet
    {
        $rules = new RuleSet();
        while ($this->reader->eol() === false) {
            $rule = $this->parseRule();
            $rules->addRule($rule);
            if ($rule->getName() === 'required') {
                $rules->setRequired(true);
            }

            if ($this->reader->isStringNext(",")) {
                $this->reader->readString(",");
            }
        }

        return $rules;
    }

    /**
     * @throws ValidationRuleParseException
     */
    private function parseRule(): Rule
    {
        $name = strtolower($this->parseAlphaNum());
        $arguments = [];

        if ($this->reader->isStringNext(":")) {
            $this->reader->readString(":");
            $arguments[] = $this->parseArgument();
        }

        return new Rule($name, $arguments);
    }

    /**
     * @throws ValidationRuleParseException
     */
    private function parseArgument(): string
    {
        return $this->parseAlphaNum();
    }

    /**
     * @throws ValidationRuleParseException
     */
    private function parseAlphaNum(): string
    {
        $result = $this->reader->readPattern('\w+');
        if ($result === false) {
            throw new ValidationRuleParseException('Failed to parse rule: ' . $this->reader);
        }

        return $result;
    }
}
