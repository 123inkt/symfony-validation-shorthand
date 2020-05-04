<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Transformer;

interface TransformerInterface
{
    /**
     * Returns true if the given can be transformed by this transformer. False otherwise.
     *
     * @param mixed $value
     */
    public function transformable($value): bool;

    /**
     * Transform a given value to the requested value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value);
}
