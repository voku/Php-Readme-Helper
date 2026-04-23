<?php

declare(strict_types=1);

namespace voku\tests;

use voku\PhpReadmeHelper\GenerateStringHelper;

/**
 * Direct unit tests for GenerateStringHelper.
 *
 * Each test targets a specific branch so that a broken implementation causes a
 * clear failure, not just a missed line.
 *
 * @internal
 */
final class GenerateStringHelperTest extends \PHPUnit\Framework\TestCase
{
    // -----------------------------------------------------------------------
    // str_replace_beginning
    // -----------------------------------------------------------------------

    public function testStrReplaceBeginningMatchesAtStart(): void
    {
        static::assertSame(
            'hi world',
            GenerateStringHelper::str_replace_beginning('hello world', 'hello', 'hi')
        );
    }

    public function testStrReplaceBeginningDoesNotReplaceInMiddle(): void
    {
        // 'world' exists in the string but NOT at position 0, so nothing changes.
        static::assertSame(
            'hello world',
            GenerateStringHelper::str_replace_beginning('hello world', 'world', 'X')
        );
    }

    public function testStrReplaceBeginningEmptyStrEmptySearchNonEmptyReplacement(): void
    {
        // str === '' and search === '' → return $replacement
        static::assertSame(
            'hello',
            GenerateStringHelper::str_replace_beginning('', '', 'hello')
        );
    }

    public function testStrReplaceBeginningEmptyStrEmptySearchEmptyReplacement(): void
    {
        // str === '' and replacement === '' (regardless of search) → return ''
        static::assertSame(
            '',
            GenerateStringHelper::str_replace_beginning('', '', '')
        );
    }

    public function testStrReplaceBeginningEmptyStrNonEmptySearchNonEmptyReplacement(): void
    {
        // str === '' but search !== '' → the strpos check fails, fall through to return $str
        static::assertSame(
            '',
            GenerateStringHelper::str_replace_beginning('', 'hello', 'X')
        );
    }

    public function testStrReplaceBeginningEmptySearchAppendsToStr(): void
    {
        // search === '' and str !== '' → return $str . $replacement
        static::assertSame(
            'hello-',
            GenerateStringHelper::str_replace_beginning('hello', '', '-')
        );
    }

    public function testStrReplaceBeginningNoMatchReturnsOriginal(): void
    {
        static::assertSame(
            'foobar',
            GenerateStringHelper::str_replace_beginning('foobar', 'baz', 'X')
        );
    }

    // -----------------------------------------------------------------------
    // replace
    // -----------------------------------------------------------------------

    public function testReplaceIsCaseSensitiveByDefault(): void
    {
        // Upper-case 'HELLO' should not match lower-case 'hello' when case_sensitive=true.
        static::assertSame(
            'HELLO world',
            GenerateStringHelper::replace('HELLO world', 'hello', 'hi')
        );
    }

    public function testReplaceIsCaseInsensitiveWhenFalse(): void
    {
        // With case_sensitive=false, 'HELLO' should be replaced.
        static::assertSame(
            'hi world',
            GenerateStringHelper::replace('HELLO world', 'hello', 'hi', false)
        );
    }

    public function testReplaceAllOccurrences(): void
    {
        static::assertSame(
            'X X X',
            GenerateStringHelper::replace('a a a', 'a', 'X')
        );
    }

    // -----------------------------------------------------------------------
    // css_identifier
    // -----------------------------------------------------------------------

    public function testCssIdentifierBasic(): void
    {
        static::assertSame(
            'hello-world',
            GenerateStringHelper::css_identifier('hello world')
        );
    }

    public function testCssIdentifierStripsInvalidChars(): void
    {
        // '!' is not a valid CSS character and should be stripped.
        static::assertSame(
            'hello-world',
            GenerateStringHelper::css_identifier('hello/world!!!')
        );
    }

    public function testCssIdentifierLowerCaseByDefault(): void
    {
        static::assertSame(
            'hello',
            GenerateStringHelper::css_identifier('HELLO')
        );
    }

    public function testCssIdentifierNoLowerCaseWhenDisabled(): void
    {
        static::assertSame(
            'HELLO',
            GenerateStringHelper::css_identifier('HELLO', [' ' => '-', '/' => '-', '[' => '', ']' => ''], false, false)
        );
    }

    public function testCssIdentifierStripeTagsEnabled(): void
    {
        // HTML tags should be removed when stripe_tags is true.
        static::assertSame(
            'hello',
            GenerateStringHelper::css_identifier('<b>Hello</b>', [' ' => '-', '/' => '-', '[' => '', ']' => ''], true)
        );
    }

    public function testCssIdentifierPreservesDoubleUnderscore(): void
    {
        // '__' must survive the filter pass.
        $result = GenerateStringHelper::css_identifier('foo__bar');
        static::assertSame('foo__bar', $result);
    }

    public function testCssIdentifierEmptyStringReturnsNonEmptyAutoId(): void
    {
        // An all-whitespace input triggers the uniqid fallback, producing a non-empty string.
        $result = GenerateStringHelper::css_identifier('   ');
        static::assertNotSame('', $result);
    }

    public function testCssIdentifierLeadingDigitIsPrefixed(): void
    {
        // A CSS identifier must not start with a digit.
        $result = GenerateStringHelper::css_identifier('123foo');
        static::assertSame('_23foo', $result);
    }
}
