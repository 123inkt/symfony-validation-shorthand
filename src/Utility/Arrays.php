<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

class Arrays
{
    /**
     * @param Path[] $paths
     */
    public static function findData(array $paths, array $data): ?array
    {
        $result = [];
        $cursor = $data;
        $length = count($paths);
        $route  = [];

        for ($i = 0; $i < $length; $i++) {
            if (is_array($cursor) === false) {
                return null;
            }

            $path = $paths[$i];
            if ($path->isWildcard()) {
                $currentRoute = count($route) === 0 ? '' : implode('.', $route) . '.';

                foreach ($cursor as $index => $entry) {
                    if ($i < $length - 1) {
                        foreach (self::findData(array_slice($paths, $i + 1), $entry) as $k => $v) {
                            $result[$currentRoute . $index . '.' . $k] = $v;
                        }
                    } else {
                        $result[$currentRoute . $index] = $entry;
                    }
                }
                return $result;
            }

            $key = $path->getKey();
            if (array_key_exists($key, $cursor) === false) {
                return null;
            }

            $route[] = $key;
            $cursor  = $cursor[$key];
        }

        return [implode('.', $route) => $cursor];
    }
}
