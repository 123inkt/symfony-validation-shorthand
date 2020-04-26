<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Rule\Parser;

class StringReader
{
    /** @var string */
    private $string;

    /** @var int */
    private $offset = 0;

    /** @var int */
    private $length;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->length = strlen($string);
    }

    public function eol(): bool
    {
        return $this->offset >= $this->length;
    }

    /**
     * Scan the given string in the buffer. This doesn't move the cursor
     */
    public function isStringNext(string $str): bool
    {
        return strpos($this->string, $str, $this->offset) === $this->offset;
    }

    /**
     * Read the given string from the buffer. If the buffer doesn't match the string, false is returned and cursor isn't moved.
     */
    public function readString(string $str): bool
    {
        if ($this->isStringNext($str) === false) {
            return false;
        }

        // move cursor
        $this->offset += strlen($str);

        return true;
    }

    /**
     * Read all whitespace
     */
    public function readWhiteSpace(): self
    {
        $this->readPattern("\s*");

        return $this;
    }

    /**
     * Read all the characters that match the pattern and move the cursor.
     *
     * @param string $pattern the regex to match. The pattern always matches the start of the string, taking offset into account.
     * @param array|null $matches
     *
     * @return bool|string false if we found no matches
     */
    public function readPattern($pattern, &$matches = null)
    {
        if (preg_match("/" . $pattern . "/A", $this->string, $matches, 0, $this->offset) !== 1) {
            return false;
        }

        // get the result
        $result = $matches[0];

        // move the pointer
        $this->offset += strlen($result);

        return $result;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
