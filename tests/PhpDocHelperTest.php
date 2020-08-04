<?php

declare(strict_types=1);

namespace voku\tests;

/**
 * @internal
 */
final class PhpDocHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testApi(): void
    {
        $result = (new \voku\PhpReadmeHelper\GenerateApi())->generate(
            __DIR__ . '/',
            __DIR__ . '/fixtures/base.md',
            [
                Dummy::class,
                DummyInterface::class,
            ]
        );

        static::assertSame(
            '[//]: # (AUTO-GENERATED BY "PHP README Helper": base file -> tests/fixtures/base.md)

# 😋 FOO BAR

## Description

Lorem ipsum dolor sit amet, consetetur sadipscing elitr, 
sed diam nonumy eirmod tempor invidunt ut labore et dolore 
magna aliquyam erat, sed diam voluptua. At vero eos et accusam 
et justo duo dolores et ea rebum. Stet clita kasd gubergren, 
no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem 
ipsum dolor sit amet, consetetur sadipscing elitr, sed diam 
nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam 
erat, sed diam voluptua. At vero eos et accusam et justo duo 
dolores et ea rebum. Stet clita kasd gubergren, no sea takimata 
sanctus est Lorem ipsum dolor sit amet.

# FOO BAR | API

Lorem ipsum dolor sit amet, consetetur ...

## Class methods

<p id="voku-php-readme-class-methods"></p><table><tr><td><a href="#vokutestsdummy-withemptyparamtypephpdocbool-parsedparamtag-array">withEmptyParamTypePhpDoc</a>
</td><td><a href="#vokutestsdummy-withphpdocparamintnull-userandint">withPhpDocParam</a>
</td><td><a href="#vokutestsdummy-withpsalmphpdoconlyparamtodo-__not_detected__-userandint">withPsalmPhpDocOnlyParam</a>
</td><td><a href="#vokutestsdummy-withreturntype-arrayintint">withReturnType</a>
</td></tr><tr><td><a href="#vokutestsdummy-withoutphpdocparambool-userandint-intstringnull">withoutPhpDocParam</a>
</td><td><a href="#vokutestsdummy-withoutreturntype-falseint">withoutReturnType</a>
</td></tr></table>

## withEmptyParamTypePhpDoc(bool $parsedParamTag): array
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
- `bool $parsedParamTag <p>some more info ...</p>`

**Return:**
- `array`

--------

## withPhpDocParam(int[]|null $useRandInt): 
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
- `int[]|null $useRandInt`

**Return:**
- `TODO: __not_detected__`

--------

## withPsalmPhpDocOnlyParam(TODO: __not_detected__ $useRandInt): 
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
- `?list<int> $useRandInt`

**Return:**
- `TODO: __not_detected__`

--------

## withReturnType(): array<int,int>
<a href="#voku-php-readme-class-methods">↑</a>
this is a test

EXAMPLE: <code>
Dummy->withReturnType(); // [1, 2, 3]
<code>

**Parameters:**
__nothing__

**Return:**
- `array<int,int>`

--------

## withoutPhpDocParam(bool $useRandInt): int[]|string[]|null
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
- `bool $useRandInt`

**Return:**
- `int[]|string[]|null <p>foo</p>`

--------

## withoutReturnType(): false|int
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
__nothing__

**Return:**
- `false|int`

--------


## Interface methods

<p id="voku-php-readme-class-methods"></p><table><tr><td><a href="#vokutestsdummyinterface-barbool-foo-nullstring-foo2-bool">bar</a>
</td><td><a href="#vokutestsdummyinterface-foo-bool">foo</a>
</td></tr></table>

## bar(bool $foo, null|string $foo2): bool
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
- `bool $foo`
- `null|string $foo2`

**Return:**
- `bool`

--------

## foo(): bool
<a href="#voku-php-readme-class-methods">↑</a>


**Parameters:**
__nothing__

**Return:**
- `bool`

--------
',
            $result
        );
    }
}
