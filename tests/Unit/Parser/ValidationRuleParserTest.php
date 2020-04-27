<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser
 * @covers ::__construct
 */
class ValidationRuleParserTest extends TestCase
{
    /**
     * @covers ::parse
     * @covers ::parseRules
     */
    public function testFailParseInvalidFieldName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field names should be string. Field type');

        $parser = new ValidationRuleParser([1 => 'a']);
        $parser->parse();
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     */
    public function testFailParseRuleWithBadRuleType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid rule definition type. Expecting string or constraint');
        (new ValidationRuleParser(['username' => [200]]))->parse();
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     */
    public function testParseRuleWithSingleConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $parser     = new ValidationRuleParser(['username' => $constraint]);
        $result     = $parser->parse();
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
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $parser = new ValidationRuleParser(['username' => 'required']);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $parser = new ValidationRuleParser(['username' => 'max:123']);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $parser = new ValidationRuleParser(['username' => 'between:5,10']);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $parser = new ValidationRuleParser(['phone-number' => 'regex:/^\d+$/i']);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithMultipleStringRules(): void
    {
        $parser = new ValidationRuleParser(['username' => 'required|max:30']);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithMultipleStringRulesAsArray(): void
    {
        $parser = new ValidationRuleParser(['username' => ['required', 'max:30']]);
        $result = $parser->parse();
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
     */
    public function testParseRuleWithStringRuleAndConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $parser     = new ValidationRuleParser(['username' => ['required', $constraint]]);
        $result     = $parser->parse();
        static::assertCount(1, $result);
        static::assertArrayHasKey('username', $result);
        static::assertInstanceOf(RuleSet::class, $result['username']);
        static::assertEquals([new Rule('required', []), $constraint], $result['username']->getRules());
    }
}
