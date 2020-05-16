<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Utility;

use InvalidArgumentException;

class Arrays
{
    private const ERROR_ALREADY_ASSIGNED = "Can't assign value to `%s` as `%s` is already assigned.";
    private const ERROR_NOT_ARRAY        = "Can't assign value to `%s` as `%s` is not an array.";

    /**
     * Recursively assign the value at given path to the array.
     * Example:
     *   array: []
     *   path: ['a', 'b']
     *   value: 'c'
     * Result:
     *   array: ['a' => ['b' => 'c']]
     *
     * @param array<mixed> $array The array to be assigned. Intentionally by reference for internal recursion
     * @param string[]     $path  The string array path to which to assign the value
     * @param mixed        $value The value to be assigned to the array
     * @return array<mixed> For convenience return the same array that was given
     * @throws InvalidArrayPathException Thrown when the given path will result in overwriting an existing non array value.
     */
    public static function assignToPath(array &$array, array $path, $value): array
    {
        if (count($path) === 0) {
            throw new InvalidArgumentException("\$path can't be empty");
        }

        // reached the tail, try to assign the value
        /** @var string $key */
        $key = array_shift($path);
        if (count($path) === 0) {
            if (array_key_exists($key, $array)) {
                throw new InvalidArrayPathException(sprintf(self::ERROR_ALREADY_ASSIGNED, gettype($array), implode('.', $path)));
            }
            $array[$key] = $value;
            return $array;
        }

        // create child array if necessary to build the path
        if (array_key_exists($key, $array) === false) {
            $array[$key] = [];
        } elseif (is_array($array[$key]) === false) {
            throw new InvalidArrayPathException(sprintf(self::ERROR_NOT_ARRAY, gettype($array), implode('.', $path)));
        }

        // continue assigned the value
        self::assignToPath($array[$key], $path, $value);
        return $array;
    }
}
