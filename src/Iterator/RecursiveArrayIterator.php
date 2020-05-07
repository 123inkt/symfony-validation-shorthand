<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Iterator;

/**
 * Recursively iterate over all children, invoking the given callback.
 */
class RecursiveArrayIterator
{
    /** @var mixed[] */
    private $data;

    /** @var callable(string $key, mixed $value): mixed */
    private $callback;

    /**
     * @param mixed[] $data
     * @param callable(string $key, mixed $value): mixed $callback
     */
    public function __construct(array $data, callable $callback)
    {
        $this->data     = $data;
        $this->callback = $callback;
    }

    /**
     * @param callable(string $key, mixed $value): mixed $callback
     */
    public function iterate(): array
    {
        return $this->walkArray('', $this->data);
    }

    /**
     * Recursively walk over the array, calling the callback function.
     * The "key" will contain the full path of the array. eg
     *
     * [a => [b => [c => d]]]
     *
     * will be called with
     *   $key   = 'a.b.c'
     *   $value = 'd'
     */
    private function walkArray(string $prefix, array $data): array
    {
        foreach ($data as $key => $value) {
            $path = $prefix . $key;

            if (is_array($value)) {
                $data[$key] = $this->walkArray($path . '.', $value);
            } else {
                $data[$key] = call_user_func($this->callback, $path, $value);
            }
        }

        return $data;
    }
}
