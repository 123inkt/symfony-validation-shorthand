<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleList;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
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
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @throws RequestValidationException
     */
    public function testFailParseRuleWithBadRuleType(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Invalid rule definition type. Expecting string or Symfony\Component\Validator\Constraint');
        $this->parser->parseRules(200);
    }

    /**
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $ruleSet    = new RuleList();
        $ruleSet->addRule($constraint);

        static::assertEquals($ruleSet, $this->parser->parseRules($constraint));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::normalizeRuleName
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('required'));

        $this->assertEquals($ruleSet, $this->parser->parseRules('required'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('max', ['123']));

        $this->assertEquals($ruleSet, $this->parser->parseRules('max:123'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('between', ['5', '10']));

        $this->assertEquals($ruleSet, $this->parser->parseRules('between:5,10'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('regex', ['/^\d+$/i']));

        $this->assertEquals($ruleSet, $this->parser->parseRules('regex:/^\d+$/i'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRules(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        $this->assertEquals($ruleSet, $this->parser->parseRules('required|max:30'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRulesAsArray(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        $this->assertEquals($ruleSet, $this->parser->parseRules(['required', 'max:30']));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithStringRuleAndConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $ruleSet    = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule($constraint);

        $this->assertEquals($ruleSet, $this->parser->parseRules(['required', $constraint]));
    }

    /**
     * @covers ::normalizeRuleName
     * @throws RequestValidationException
     */
    public function testParseRuleWithRuleNormalization(): void
    {
        $ruleSet    = new RuleList();
        $ruleSet->addRule(new Rule('integer', []));
        $ruleSet->addRule(new Rule('boolean', []));

        $this->assertEquals($ruleSet, $this->parser->parseRules('int|bool'));
    }
}
