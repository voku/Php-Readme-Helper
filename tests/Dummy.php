<?php

declare(strict_types=1);

namespace voku\tests;

/**
 * @internal
 *
 * @property int $foo
 * @property string $bar
 */
final class Dummy extends \stdClass
{
    /**
     * @var null|int[]
     *
     * @phpstan-var null|array<int,int>
     */
    public $lall1 = [];

    /**
     * @var float
     */
    public $lall2 = 0.1;

    /**
     * @var null|float
     */
    public $lall3;

    const FOO_BAR = 4;

    /**
     * this is a test
     *
     * EXAMPLE: <code>
     * Dummy->withReturnType(); // [1, 2, 3]
     * <code>
     *
     * @return array<int, int>
     */
    public function withReturnType(): array
    {
        return [1, 2, 3];
    }

    /**
     * @return false|int
     */
    public function withoutReturnType()
    {
        return \random_int(0, 10) > 5 ? 0 : false;
    }

    /**
     * @param callable(string): string $callback
     *
     * @return string
     */
    public function withCallback($callback)
    {
        return $callback('foo');
    }

    /**
     * @return int[]|string[]|null <p>foo</p>
     *
     * @psalm-return ?list<int|string>
     */
    public function withoutPhpDocParam(bool $useRandInt = true): ?array {
        return \random_int(0, 10) > 5 ? [1, 2, 'lall'] : null;
    }

    /**
     * @param int[]|null $useRandInt
     *
     * @psalm-param ?list<int> $useRandInt
     *                                    <p>foo öäü bar</p>
     */
    public function withPhpDocParam($useRandInt = [3, 5])
    {
        $max = $useRandInt === null ? 5 : \max($useRandInt);

        return \random_int(0, $max) > 2 ? [1, 2, 'lall'] : null;
    }

    /**
     * @psalm-param ?list<int> $useRandInt
     */
    public function withPsalmPhpDocOnlyParam($useRandInt = [3, 5])
    {
        $max = $useRandInt === null ? 5 : \max($useRandInt);

        return \random_int(0, $max) > 2 ? [1, 2, 'lall'] : null;
    }

    /**
     * @param bool $parsedParamTag
     *                        <p>some more info ...</p>
     *
     * @return array
     *
     * @psalm-return array{parsedParamTagStr: string, variableName: null[]|string}
     */
    public static function withEmptyParamTypePhpDoc($parsedParamTag): array {
        return [
            'parsedParamTagStr' => 'foo',
            'variableName'      => [null],
        ];
    }

    /**
     * @return bool
     */
    private function privateDoNotShowInTheDoc(): bool {
        return true;
    }
}
