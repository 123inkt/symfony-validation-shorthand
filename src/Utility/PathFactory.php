<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

class PathFactory
{
    /**
     * @return Path[]
     */
    public static function createFromString(string $pathDefinition): array
    {
        $paths = [];
        $parts = explode('.', $pathDefinition);

        foreach ($parts as $part) {
            $paths[] = $path = new Path($part);

            if ($part === '*') {
                $path->setWildcard(true);
                continue;
            }

            if (str_ends_with($part, '?')) {
                $part = substr($part, -1);
                $path->setKey($part);
                $path->setOptional(true);
            }

            if (str_starts_with($part, '#') && preg_match('/^#(\d+)$/', $part, $matches) === 1) {
                $path->setKey($matches[1]);
                $path->setOffset((int)$matches[1]);
            }
        }

        return $paths;
    }
}
