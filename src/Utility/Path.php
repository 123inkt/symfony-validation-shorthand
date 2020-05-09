<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

class Path
{
    /** @var string */
    private $path;

    /** @var string|null */
    private $key;

    /** @var int|null */
    private $offset;

    /** @var bool */
    private $wildcard = false;

    /** @var bool */
    private $optional = false;


    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key ?? $this->path;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function isOffset(): bool
    {
        return $this->offset !== null;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function isWildcard(): bool
    {
        return $this->wildcard;
    }

    public function setWildcard(bool $wildcard): self
    {
        $this->wildcard = $wildcard;
        return $this;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function setOptional(bool $optional): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function __toString()
    {
        return $this->getKey();
    }
}
