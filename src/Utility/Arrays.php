<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

use InvalidArgumentException;

class Arrays
{
    /**
     * Recursively assign the value at given path to the array.
     * Example:
     *   array: []
     *   path: ['a', 'b']
     *   value: 'c'
     * Result:
     *   array: ['a' => ['b' => 'c']]
     *
     * @param mixed    $array The array to be assigned. Intentionally by reference for internal recursion
     * @param string[] $path  The string array path to which to assign the value
     * @param mixed    $value The value to be assigned to the array
     * @return array For convenience return the same array that was given
     * @throws InvalidArrayPathException Thrown when the given path will result in overwriting an existing non array value.
     */
    public static function assignToPath(array &$array, array $path, $value): array
    {
        if (count($path) === 0) {
            throw new InvalidArgumentException("\$path can't be empty");
        }

        $key = array_shift($path);
        if (count($path) === 0) {
            $array[$key] = $value;
            return $array;
        }

        if (array_key_exists($key, $array) === false) {
            $array[$key] = [];
        } elseif (is_array($array[$key]) === false) {
            throw new InvalidArrayPathException(
                "Can't assign value to `" . print_r($array, true) . "` as `" . implode('.', $path) . "` is not array"
            );
        }

        self::assignToPath($array[$key], $path, $value);
        return $array;
    }
}
