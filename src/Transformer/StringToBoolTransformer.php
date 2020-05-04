<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Transformer;

use function filter_var;

class StringToBoolTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transformable($value): bool
    {
        return is_bool($value) || (is_string($value) && $this->filter($value)) !== null;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return (bool)$this->filter($value);
    }

    /**
     * @param mixed $value
     */
    private function filter($value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE | FILTER_REQUIRE_SCALAR]);
    }
}
