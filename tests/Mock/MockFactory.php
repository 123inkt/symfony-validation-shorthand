<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Mock;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class MockFactory
{
    /**
     * @return TranslatorInterface&MockObject
     */
    public static function createTranslator(TestCase $testCase): MockObject
    {
        $translatorMock = $testCase->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $translatorMock->method('trans')->willReturn('unit test');

        return $translatorMock;
    }
}
