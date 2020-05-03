<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;

class Rule
{
    public const RULE_REQUIRED = 'required';
    public const RULE_BOOLEAN  = 'boolean';
    public const RULE_INTEGER  = 'integer';
    public const RULE_FLOAT    = 'float';
    public const RULE_EMAIL    = 'email';
    public const RULE_REGEX    = 'regex';
    public const RULE_MIN      = 'min';
    public const RULE_MAX      = 'max';
    public const RULE_BETWEEN  = 'between';

    public const ALLOWED_RULES = [
        self::RULE_REQUIRED,
        self::RULE_BOOLEAN,
        self::RULE_INTEGER,
        self::RULE_FLOAT,
        self::RULE_EMAIL,
        self::RULE_REGEX,
        self::RULE_MIN,
        self::RULE_MAX,
        self::RULE_BETWEEN,
    ];

    /** @var string */
    private $name;

    /** @var string[] */
    private $parameters;

    /**
     * @param string[] $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        $this->name       = $name;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws RequestValidationException
     */
    public function getParameter(int $offset): string
    {
        if (isset($this->parameters[$offset]) === false) {
            throw new RequestValidationException('Rule `' . $this->getName() . '` expects at least ' . $offset . ' parameter(s)');
        }

        return $this->parameters[$offset];
    }

    /**
     * @throws RequestValidationException
     */
    public function getIntParam(int $offset): int
    {
        $argument = $this->getParameter($offset);
        if ((string)(int)$argument !== $argument) {
            throw new RequestValidationException(
                'Rule `' . $this->getName() . '` expects parameter #' . $offset . ' to be an int. Encountered: `' . $argument . '`'
            );
        }

        return (int)$argument;
    }

    /**
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
