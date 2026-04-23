<?php

declare(strict_types=1);

namespace voku\tests;

use voku\PhpReadmeHelper\Template\TemplateFormatter;

/**
 * Direct unit tests for Template\TemplateFormatter.
 *
 * @internal
 */
final class TemplateFormatterTest extends \PHPUnit\Framework\TestCase
{
    public function testSetAndFormat(): void
    {
        $formatter = new TemplateFormatter('Hello %name%!');
        $formatter->set('name', 'World');

        static::assertSame('Hello World!', $formatter->format());
    }

    public function testSetOverwritesPreviousValue(): void
    {
        $formatter = new TemplateFormatter('%val%');
        $formatter->set('val', 'first');
        $formatter->set('val', 'second');

        static::assertSame('second', $formatter->format());
    }

    public function testUnsetPlaceholderRemainsInOutput(): void
    {
        // If a placeholder is never set it stays verbatim in the output.
        $formatter = new TemplateFormatter('%missing%');

        static::assertSame('%missing%', $formatter->format());
    }

    public function testAppendCreatesNewVar(): void
    {
        $formatter = new TemplateFormatter('%data%');
        $formatter->append('data', 'hello');

        static::assertSame('hello', $formatter->format());
    }

    public function testAppendAccumulatesValues(): void
    {
        $formatter = new TemplateFormatter('%items%');
        $formatter->append('items', 'a');
        $formatter->append('items', ', b');
        $formatter->append('items', ', c');

        static::assertSame('a, b, c', $formatter->format());
    }

    public function testAppendAfterSetAddsToExistingValue(): void
    {
        $formatter = new TemplateFormatter('%msg%');
        $formatter->set('msg', 'Hello');
        $formatter->append('msg', ' World');

        static::assertSame('Hello World', $formatter->format());
    }

    public function testMultiplePlaceholders(): void
    {
        $formatter = new TemplateFormatter('%greeting% %name%!');
        $formatter->set('greeting', 'Hi');
        $formatter->set('name', 'PHP');

        static::assertSame('Hi PHP!', $formatter->format());
    }

    public function testFormatReturnsSameResultOnSecondCall(): void
    {
        // format() must be idempotent – calling it twice gives the same result.
        $formatter = new TemplateFormatter('%x%');
        $formatter->set('x', '42');

        static::assertSame($formatter->format(), $formatter->format());
    }
}
