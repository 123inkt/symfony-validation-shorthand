<?php


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
     * @return RuleInfo[]
     * @throws ValidationRuleParseException
     */
    public function parseRules(): array
    {
        $rules = [];
        while ($this->reader->eol() === false) {
            $rules[] = $this->parseRule();

            if ($this->reader->isStringNext(",")) {
                $this->reader->readString(",");
            }
        }

        return $rules;
    }

    /**
     * @throws ValidationRuleParseException
     */
    private function parseRule(): RuleInfo
    {
        $name = $this->parseAlphaNum();
        $arguments = [];

        if ($this->reader->isStringNext(":")) {
            $this->reader->readString(":");
            $arguments[] = $this->parseArgument();
        }

        return new RuleInfo($name, $arguments);
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
