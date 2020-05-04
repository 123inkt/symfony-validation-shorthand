<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Transformer;

use function is_float;
use function is_int;
use function is_string;
use function preg_match;

class StringToFloatTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transformable($value): bool
    {
        return (is_string($value) && preg_match('/^-?[1-9]\d*$/', $value) === 1) || is_int($value) || is_float($value);
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        return (float)$value;
    }
}
