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
        $result = \voku\PhpReadmeHelper\GenerateApi::generate(
            __DIR__ . '/Dummy.php',
            __DIR__ . '/fixtures/base.md',
            [Dummy::class]
        );

        static::assertSame(
            '
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

<table><tr><td><a href="#withemptyparamtypephpdocparsedparamtag-array">withEmptyParamTypePhpDoc</a>
</td><td><a href="#withphpdocparamintnull-userandint">withPhpDocParam</a>
</td><td><a href="#withpsalmphpdoconlyparamuserandint">withPsalmPhpDocOnlyParam</a>
</td><td><a href="#withreturntype-arrayintint">withReturnType</a>
</td></tr><tr><td><a href="#withoutphpdocparamuserandint-intstringnull">withoutPhpDocParam</a>
</td><td><a href="#withoutreturntype-falseint">withoutReturnType</a>
</td></tr></table>

## withEmptyParamTypePhpDoc($parsedParamTag): array
<a href="#class-methods">↑</a>


**Parameters:**
- ``

**Return:**
- `array`

--------

## withPhpDocParam(int[]|null $useRandInt): 
<a href="#class-methods">↑</a>


**Parameters:**
- `array<array-key, int>|null $useRandInt`

**Return:**
- `__not_detected__`

--------

## withPsalmPhpDocOnlyParam($useRandInt): 
<a href="#class-methods">↑</a>


**Parameters:**
- `list<int>|null`

**Return:**
- `__not_detected__`

--------

## withReturnType(): array<int,int>
<a href="#class-methods">↑</a>
this is a test

EXAMPLE: <code>
Dummy->withReturnType(); // [1, 2, 3]
<code>

**Parameters:**
__nothing__

**Return:**
- `array<int,int>`

--------

## withoutPhpDocParam($useRandInt): int[]|string[]|null
<a href="#class-methods">↑</a>


**Parameters:**
- ``

**Return:**
- `int[]|string[]|null <p>foo</p>`

--------

## withoutReturnType(): false|int
<a href="#class-methods">↑</a>


**Parameters:**
__nothing__

**Return:**
- `false|int`

--------
',
            $result
        );
    }
}
