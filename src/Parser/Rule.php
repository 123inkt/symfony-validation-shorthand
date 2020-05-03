<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;

class Rule
{
    /** @var string */
    private $name;

    /** @var string[] */
    private $parameters;

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
            throw new RequestValidationException('Unknown offset for rule: ' . $this->getName() . ', offset:' . $offset);
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
            throw new RequestValidationException('Invalid int argument for rule: ' . $this->getName() . ', value: ' . $this->parameters[$offset]);
        }

        return (int)$argument;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
