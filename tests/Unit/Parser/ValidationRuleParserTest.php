<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\ConstraintResolverMockHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser
 */
class ValidationRuleParserTest extends TestCase
{
    /** @var ValidationRuleParser */
    private $parser;

    /** @var ConstraintResolverMockHelper */
    private $resolverMockHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $resolver                 = $this->createMock(ConstraintResolver::class);
        $this->resolverMockHelper = new ConstraintResolverMockHelper($resolver);
        $this->parser             = new ValidationRuleParser($resolver);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testFailParseInvalidFieldName(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Field names should be string. Field type is: ');
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
        $this->expectException(RequestValidationException::class);
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
        $optional   = new Assert\Optional($constraint);
        $ruleSet    = new RuleSet();
        $ruleSet->addRule($constraint);

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => $constraint]));
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
        $required = new Assert\Required();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('required'));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $required);
        $this->assertCollection(['username' => $required], $this->parser->parse(['username' => 'required']));
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

    private function assertCollection(array $fields, Collection $actual, string $message = '')
    {
        static::assertEquals(new Assert\Collection(['fields' => $fields]), $actual, $message);
    }
}
