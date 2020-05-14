<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Integration;

use ArrayIterator;
use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversNothing
 */
class TraversableArrayValidationTest extends TestCase
{
    /** @var ConstraintFactory */
    private $constraintFactory;

    /** @var ValidatorInterface */
    private $validator;


    protected function setUp(): void
    {
        parent::setUp();
        $this->constraintFactory = new ConstraintFactory();
        $this->validator         = Validation::createValidator();
    }

    /**
     * @throws Exception
     */
    public function testSingleDimensionArrayValidation(): void
    {
        $rules = ['*' => 'int'];

        $iterator = new ArrayIterator([1, 2]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        $iterator = new ArrayIterator([]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        $iterator = new ArrayIterator([1, 'a']);
        static::assertCount(1, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));
    }

    /**
     * @throws Exception
     */
    public function testSingleDimensionArrayWithRequiredElement(): void
    {
        $rules = ['*' => 'required|int'];

        $iterator = new ArrayIterator([]);
        static::assertCount(1, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        $iterator = new ArrayIterator([3]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        $iterator = new ArrayIterator(['a']);
        static::assertCount(1, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));
    }

    /**
     * @throws Exception
     */
    public function testTraversableArrayWithColumnData(): void
    {
        $rules = [
            '*.0' => 'required|int',
            '*.1' => 'string'
        ];

        // empty array
        $iterator = new ArrayIterator([]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // one entry: integer
        $iterator = new ArrayIterator([[5]]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // one entry: integer + string
        $iterator = new ArrayIterator([[5, 'test']]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // one entry: missing integer
        $iterator = new ArrayIterator([[1 => 'test']]);
        static::assertCount(1, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // double entries
        $iterator = new ArrayIterator([[1, 'test'], [2, 'unit']]);
        static::assertCount(0, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // invalid double entries
        $iterator = new ArrayIterator([[1, 2], [3, 4]]);
        static::assertCount(2, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));

        // invalid nesting
        $iterator = new ArrayIterator(['a', 'b']);
        static::assertCount(2, $this->validator->validate($iterator, $this->constraintFactory->fromRuleDefinitions($rules)));
    }
}
