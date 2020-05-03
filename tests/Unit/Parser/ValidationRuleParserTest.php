<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser
 */
class ValidationRuleParserTest extends TestCase
{
    /** @var ValidationRuleParser */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ValidationRuleParser();
    }


    /**
     * @covers ::parse
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testFailParseInvalidFieldName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field names should be string. Field type');
        $this->parser->parse([1 => 'a']);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @throws RequestValidationException
     */
    public function testFailParseRuleWithBadRuleType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid rule definition type. Expecting string or constraint');
        $this->parser->parse(['username' => [200]]);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $result     = $this->parser->parse(['username' => $constraint]);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertSame([$constraint], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $result = $this->parser->parse(['username' => 'required']);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('required')], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $result = $this->parser->parse(['username' => 'max:123']);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('max', ['123'])], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $result = $this->parser->parse(['username' => 'between:5,10']);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('between', ['5', '10'])], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $result = $this->parser->parse(['phone-number' => 'regex:/^\d+$/i']);
        static::assertCount(1, $result);
        static::assertArrayHasKey('phone-number', $result);
        static::assertInstanceOf(RuleSet::class, $result['phone-number']);
        static::assertEquals([new Rule('regex', ['/^\d+$/i'])], $result['phone-number']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRules(): void
    {
        $result = $this->parser->parse(['username' => 'required|max:30']);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('required', []), new Rule('max', ['30'])], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRulesAsArray(): void
    {
        $result = $this->parser->parse(['username' => ['required', 'max:30']]);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('required', []), new Rule('max', ['30'])], $result['username']->getRules());
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithStringRuleAndConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $result     = $this->parser->parse(['username' => ['required', $constraint]]);
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('required', []), $constraint], $result['username']->getRules());
    }
}
