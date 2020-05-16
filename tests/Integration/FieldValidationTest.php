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
     * @param mixed $data
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
     * @param mixed $data
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

    public function dataProviderRequiredFields(): Generator
    {
        yield "required: success" => ['required', 'unit test', true];
        yield "required + empty: success" => ['required', '', true];
        yield "required + nullable: success" => ['required|nullable', null, true];
        yield "required nullable: fails" => ['required', null, false];

        // field can't be null or empty string
        yield "required + filled: true" => ['required', 'unit test', true];
        yield "required + filled: false" => ['required|filled', '', false];
        yield "required + filled: false" => ['required|filled', null, false];

        // field can be null or filled string, but not empty string
        yield "required + filled + nullable: true" => ['required|filled|nullable', 'unit test', true];
        yield "required + filled + nullable: true" => ['required|filled|nullable', null, true];
        yield "required + filled + nullable: false" => ['required|filled|nullable', '', false];

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

        // field should be integer or integer castable
        yield "required + int: true" => ['required|int', 3, true];
        yield "required + int: true" => ['required|int', '3', true];
        yield "required + int: false" => ['required|int', '3a', false];
        yield "required + int: false" => ['required|int', [], false];

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
        yield "required + int + min size + nullable: true" => ['required|int|nullable|min:3', 3, true];
        yield "required + int + min size + nullable: true" => ['required|int|nullable|min:3', null, true];
        yield "required + int + min size + nullable: false" => ['required|int|min:3', null, false];

        // field should be boolean or boolean castable
        yield "required + bool: true" => ['required|bool', true, true];
        yield "required + bool: true" => ['required|bool', false, true];
        yield "required + bool: false" => ['required|bool', null, false];
        yield "required + bool + nullable: false" => ['required|bool|nullable', true, true];
        yield "required + bool + nullable: false" => ['required|bool|nullable', null, true];
        yield "required + bool + int: true" => ['required|bool', 1, true];
        yield "required + bool + int: true" => ['required|bool', 0, true];
        yield "required + bool + string: true" => ['required|bool', '1', true];
        yield "required + bool + string: true" => ['required|bool', '0', true];
        yield "required + bool + string: true" => ['required|bool', 'on', true];
        yield "required + bool + string: true" => ['required|bool', 'off', true];
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
        yield "required + rule + constraint: false" => [['required', 'string', new Assert\NotBlank()], '', false];
        yield "required + rule + constraint: false" => [['required', 'string', new Assert\NotBlank()], 5, false];
    }

    public function dataProviderOptionalFields(): Generator
    {
        yield "optional: null: success" => ['string', false, false];
        yield "optional: null: success" => ['string|nullable', null, true];
        yield "optional: required + string: fail" => ['required|string', false, false];
        yield "optional: string + nullable: success" => ['string|nullable', null, true];
        yield "optional: string + nullable: fail" => ['string', null, false];
        yield "optional: string + filled: fail" => ['string|filled', null, false];
        yield "optional: string + filled: fail" => ['string|filled', '', false];
        yield "optional: string + filled: success" => ['string|filled', 'test', true];
        yield "optional: string + nullable + filled: success A" => ['string|nullable|filled', null, true];
        yield "optional: string + nullable + filled: success B" => ['string|nullable|filled', 'test', true];
        yield "optional: string + nullable + filled: success C" => ['string|nullable|filled', '', false];
    }
}
