<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

class Arrays
{
    /**
     * @param mixed    $array
     * @param string[] $path
     * @param mixed    $value
     * @throws ArrayAssignException
     */
    public static function assignToPath(array &$array, array $path, $value): array
    {
        $key = array_shift($path);
        if (count($path) === 0) {
            $array[$key] = $value;
            return $array;
        }

        if (array_key_exists($key, $array) === false) {
            $array[$key] = [];
        } elseif (is_array($array[$key]) === false) {
            throw new ArrayAssignException(
                "Can't assign value to `" . print_r($array, true) . "` as `" . implode('.', $path) . "` is not array"
            );
        }

        self::assignToPath($array[$key], $path, $value);
        return $array;
    }

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
