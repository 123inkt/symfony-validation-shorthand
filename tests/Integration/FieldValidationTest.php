<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Integration;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversNothing
 */
class FieldValidationTest extends TestCase
{
    /** @var RuleParser */
    private $parser;

    /** @var ConstraintResolver */
    private $resolver;

    /** @var ValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser    = new RuleParser();
        $this->resolver  = new ConstraintResolver();
        $this->validator = Validation::createValidator();
    }

    /**
     * @param string|string[] $rules
     * @param mixed           $data
     * @dataProvider dataProviderRequiredFields
     * @throws InvalidRuleException
     */
    public function testResolverRequiredFields($rules, $data, bool $success): void
    {
        $constraint = $this->resolver->resolveRuleList($this->parser->parseRules($rules));
        $violations = $this->validator->validate(['value' => $data], new Assert\Collection(['value' => $constraint]));

        if ($success) {
            static::assertCount(0, $violations);
        } else {
            static::assertNotCount(0, $violations);
        }
    }

    /**
     * @param string|string[] $rules
     * @param mixed           $data
     * @dataProvider dataProviderOptionalFields
     * @throws InvalidRuleException
     */
    public function testResolverOptionalFields($rules, $data, bool $success): void
    {
        $constraint = $this->resolver->resolveRuleList($this->parser->parseRules($rules));
        $dataSet    = $data === false ? [] : ['value' => $data];
        $violations = $this->validator->validate($dataSet, new Assert\Collection(['value' => $constraint]));

        if ($success) {
            static::assertCount(0, $violations);
        } else {
            static::assertNotCount(0, $violations);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderRequiredFields(): Generator
    {
        yield "required: success" => ['required', 'unit test', true];
        yield "required + empty: success" => ['required', '', true];
        yield "required + nullable: success" => ['required|nullable', null, true];
        yield "required nullable: fails" => ['required', null, false];

        // field can't be null or empty string
        yield "required + filled: true" => ['required', 'unit test', true];
        yield "required + filled: false A" => ['required|filled', '', false];
        yield "required + filled: false B" => ['required|filled', null, false];

        // field must be a type of alpha
        yield "required + alpha: true A" => ['required|alpha', 'unit', true];
        yield "required + alpha: true B" => ['required|alpha', 'unitTest', true];
        yield "required + alpha: false A" => ['required|alpha', 'unit test', false];
        yield "required + alpha: false B" => ['required|alpha', 'unit-test', false];
        yield "required + alpha: false C" => ['required|alpha', 'unit5', false];
        yield "required + alpha_dash: true" => ['required|alpha_dash', 'unit-test_9', true];
        yield "required + alpha_dash: false" => ['required|alpha_dash', 'unit test 9', false];
        yield "required + alpha_num: true" => ['required|alpha_num', 'unitTest123', true];
        yield "required + alpha_num: false" => ['required|alpha_num', 'unit-test_123', false];

        // field can be null or filled string, but not empty string
        yield "required + filled + nullable: true A" => ['required|filled|nullable', 'unit test', true];
        yield "required + filled + nullable: true B" => ['required|filled|nullable', null, true];
        yield "required + filled + nullable: false" => ['required|filled|nullable', '', false];

        // field must be within a set
        yield "required + in:a true" => ['required|in:a', 'a', true];
        yield "required + in:a false" => ['required|in:a', 'b', false];
        yield "required + in:a,b true" => ['required|in:a,b', 'b', true];
        yield "required + in: false" => ['required|in:', 'b', false];

        // field should have min/max string length
        yield "required + string min length: true" => ['required|min:3', 'unit', true];
        yield "required + string min length: false" => ['required|min:5', 'unit', false];
        yield "required + string max length: true" => ['required|max:5', 'unit', true];
        yield "required + string max length: false" => ['required|max:3', 'unit', false];
        yield "required + string min+max length left: true" => ['required|between:3,5', 'tea', true];
        yield "required + string min+max length right: true" => ['required|between:3,5', 'apple', true];
        yield "required + string min+max length short: false" => ['required|between:3,5', 'id', false];
        yield "required + string min+max length long: false" => ['required|between:3,5', 'banana', false];
        // without 'integer' any value will be treated as string
        yield "required + string max with int: false" => ['required|min:10', 12345, false];
        yield "required + string max with int: true" => ['required|max:1', 9, true];
        yield "required + string type with int: true" => ['required|string', 9, false];


        // field should be array
        yield "required + array: true" => ['required|array', [], true];
        yield "required + array: false A" => ['required|array', 5, false];
        yield "required + array: false B" => ['required|array', null, false];
        yield "required + array nullable: true" => ['required|array|nullable', null, true];

        // field should be integer or integer castable
        yield "required + int: true A" => ['required|int', 3, true];
        yield "required + int: true B" => ['required|int', '3', true];
        yield "required + int: false A" => ['required|int', '3a', false];
        yield "required + int: false B" => ['required|int', [], false];

        // field should have min/max integer size
        yield "required + int + min size: true" => ['required|int|min:3', '3', true];
        yield "required + int + min size: false" => ['required|int|min:5', '4', false];
        yield "required + int + max size: true" => ['required|int|max:5', '5', true];
        yield "required + int + max size: false" => ['required|int|max:3', '4', false];
        yield "required + int + min+max size left: true" => ['required|int|between:3,5', '3', true];
        yield "required + int + min+max size right: true" => ['required|int|between:3,5', '5', true];
        yield "required + int + min+max size short: false" => ['required|int|between:3,5', '2', false];
        yield "required + int + min+max size long: false" => ['required|int|between:3,5', '6', false];

        // field nullable should supersede the min:3
        yield "required + int + min size + nullable: true A" => ['required|int|nullable|min:3', 3, true];
        yield "required + int + min size + nullable: true B" => ['required|int|nullable|min:3', null, true];
        yield "required + int + min size + nullable: false" => ['required|int|min:3', null, false];

        // field should be boolean or boolean castable
        yield "required + bool: true A" => ['required|bool', true, true];
        yield "required + bool: true B" => ['required|bool', false, true];
        yield "required + bool: false" => ['required|bool', null, false];
        yield "required + bool + nullable: false A" => ['required|bool|nullable', true, true];
        yield "required + bool + nullable: false B" => ['required|bool|nullable', null, true];
        yield "required + bool + int: true A" => ['required|bool', 1, true];
        yield "required + bool + int: true B" => ['required|bool', 0, true];
        yield "required + bool + string: true A" => ['required|bool', '1', true];
        yield "required + bool + string: true B" => ['required|bool', '0', true];
        yield "required + bool + string: true C" => ['required|bool', 'on', true];
        yield "required + bool + string: true D" => ['required|bool', 'off', true];
        yield "required + bool + string: false" => ['required|bool', 'abc', false];

        // field should be float or float castable
        yield "required + float 1: true" => ['required|float', 1, true];
        yield "required + float 1.0: true" => ['required|float', 1.0, true];
        yield "required + float '1.0': true" => ['required|float', '1.0', true];
        yield "required + float '-1.0': true" => ['required|float', '-1.0', true];
        yield "required + float '-1.0' + nullable: true" => ['required|float|nullable', '-1.0', true];
        yield "required + float '.1' + nullable: true" => ['required|float|nullable', '.1', true];
        yield "required + float '' + nullable: true" => ['required|float|nullable', '', false];
        yield "required + float null + nullable: true" => ['required|float|nullable', null, true];
        yield "required + float 'abc' + nullable: false" => ['required|float|nullable', 'abc', false];
        yield "required + float '1,0' + nullable: true" => ['required|float|nullable', '1,0', false];

        // field should be valid date
        yield "required + date: true" => ['required|date', '2020-01-01', true];
        yield "required + date: false" => ['required|date', '01-01-2020', false];
        yield "required + datetime: true" => ['required|datetime', '2020-01-01 12:15:59', true];
        yield "required + datetime: false" => ['required|datetime', '12:15:59 2020-01-01', false];
        yield "required + date_format: true" => ['required|date_format:d/m/Y', '01/01/2020', true];
        yield "required + date_format: false" => ['required|date_format:Y-m-d', '01-01-2020', false];
        yield "required + date + min '2010-01-01': true" => ['required|date|min:2010-01-01', '2010-01-01', true];
        yield "required + date + min '2010-01-01': false" => ['required|date|min:2010-01-01', '2009-12-31', false];
        yield "required + date + max '2010-01-01': true" => ['required|date|max:2010-01-01', '2009-12-31', true];
        yield "required + date + max '2010-01-01': false" => ['required|date|max:2010-01-01', '2010-01-02', false];
        yield "required + date + min '2009-12-30' + max '2010-01-01': true" => ['required|date|min:2009-12-30|max:2010-01-01', '2009-12-31', true];
        yield "required + date + between '2009-12-30' '2010-01-01': true" => ['required|date|min:2009-12-30|max:2010-01-01', '2009-12-31', true];
        yield "required + date + between '2009-12-30' '2010-01-01': false" => ['required|date|min:2009-12-30|max:2010-01-01', '2009-11-30', false];

        // field should be email
        yield "required + email: true" => ['required|email', 'test@example.com', true];
        yield "required + email + not nullable: false" => ['required|email', null, false];
        yield "required + email + nullable: true" => ['required|email|nullable', null, true];
        yield "required + email + invalid: false" => ['required|email', 'test', false];

        // field should be url
        yield "required + url: true" => ['required|url', 'http://example.com/', true];
        yield "required + url + not nullable: false" => ['required|url', null, false];
        yield "required + url + nullable: true" => ['required|url|nullable', null, true];
        yield "required + url + invalid: false" => ['required|url', 'test', false];

        // rule + constraint combination
        yield "required + rule + constraint: true" => [['required', 'string', new Assert\NotBlank()], 'test', true];
        yield "required + rule + constraint: false A" => [['required', 'string', new Assert\NotBlank()], '', false];
        yield "required + rule + constraint: false B" => [['required', 'string', new Assert\NotBlank()], 5, false];
    }

    public function dataProviderOptionalFields(): Generator
    {
        yield "optional: null: success A" => ['string', false, false];
        yield "optional: null: success B" => ['string|nullable', null, true];
        yield "optional: required + string: fail" => ['required|string', false, false];
        yield "optional: string + nullable: success" => ['string|nullable', null, true];
        yield "optional: string + nullable: fail" => ['string', null, false];
        yield "optional: string + filled: fail A" => ['string|filled', null, false];
        yield "optional: string + filled: fail B" => ['string|filled', '', false];
        yield "optional: string + filled: success" => ['string|filled', 'test', true];
        yield "optional: string + nullable + filled: success A" => ['string|nullable|filled', null, true];
        yield "optional: string + nullable + filled: success B" => ['string|nullable|filled', 'test', true];
        yield "optional: string + nullable + filled: success C" => ['string|nullable|filled', '', false];
    }
}
