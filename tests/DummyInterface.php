<?php

declare(strict_types=1);

namespace voku\tests;

interface DummyInterface
{
    /**
     * @return bool
     */
    public function foo(): bool;

    /**
     * @param bool $foo
     * @param null|string $foo2
     *
     * @return bool
     */
    public function bar(bool $foo = false, $foo2 = null): bool;
}
