<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Iterator;

class Path
{
    /** @var string[] */
    private $path = [];

    public function __construct(Path $path = null, string $key = null)
    {
        if ($path !== null) {
            $this->path = $path->path;
        }
        if ($key !== null) {
            $this->path[] = $key;
        }
    }

    public function getPath(): array
    {
        return $this->path;
    }
}
