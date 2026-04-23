<?php

declare(strict_types=1);

namespace voku\tests;

/**
 * Unit enum (no backing type) so that the getEnumCasesOutput code path that
 * formats a case WITHOUT a value is exercised.
 *
 * @internal
 */
enum DummyUnitEnum
{
    case FOO;
    case BAR;
    case BAZ;
}
