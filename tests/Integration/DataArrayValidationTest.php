<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Integration;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Exception;

/**
 * @coversNothing
 */
class DataArrayValidationTest extends AbstractIntegrationTestCase
{
    /**
     * @throws Exception
     */
    public function testRequiredOptionalFields(): void
    {
        $rules = [
            'first_name' => 'required|string',
            'last_name'  => 'string'
        ];

        // fully filled array
        static::assertHasNoViolations(['first_name' => 'Peter', 'last_name' => 'Parker'], $rules);

        // array without last name should pass
        static::assertHasNoViolations(['first_name' => 'Peter'], $rules);

        // array without first name should fail
        static::assertHasViolations(['last_name' => 'Parker'], $rules);

        // array with undefined fields should fail
        static::assertHasViolations(['first_name' => 'Peter', 'age' => 20], $rules);
    }

    /**
     * @throws Exception
     */
    public function testRequiredOptionalNestedFields(): void
    {
        // last_name is optional
        // birth_day is optional. if set day, month, and year are required
        $rules = [
            'name.first_name'  => 'required|string',
            'name.last_name'   => 'string',
            'birth_day?.day'   => 'required|int',
            'birth_day?.month' => 'required|int',
            'birth_day?.year'  => 'required|int',
        ];

        // fully filled array should pass
        $data = [
            'name'      => ['first_name' => 'Peter', 'last_name' => 'Parker'],
            'birth_day' => ['day' => '1', 'month' => '11', 'year' => '1970']
        ];
        static::assertHasNoViolations($data, $rules);

        // array without birth_day should pass
        static::assertHasNoViolations(['name' => ['first_name' => 'Peter', 'last_name' => 'Parker']], $rules);

        // array without last_name + birth_day should pass
        static::assertHasNoViolations(['name' => ['first_name' => 'Peter']], $rules);

        // array with incomplete birth_day should fail
        $data = [
            'name'      => ['first_name' => 'Peter', 'last_name' => 'Parker'],
            'birth_day' => ['day' => '1', 'month' => '11']
        ];
        static::assertHasViolations($data, $rules);

        // array without first name should fail;
        static::assertHasViolations(['name' => ['last_name' => 'Parker']], $rules);
    }

    /**
     * Test assigning incorrect paths
     *
     * @throws Exception
     */
    public function testRuleContainsInvalidPath(): void
    {
        $rules = [
            'name'            => 'string',
            'name.first_name' => 'string'
        ];

        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("'name.first_name' can't be assigned as this path already contains a non-array value.");
        $this->constraintFactory->fromRuleDefinitions($rules);
    }
}
