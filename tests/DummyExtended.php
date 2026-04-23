<?php

declare(strict_types=1);

namespace voku\tests;

/**
 * Extended dummy class with edge-case properties and methods for testing
 * various GenerateApi configuration flags.
 *
 * @internal
 */
class DummyExtended
{
    /**
     * Public property declared with a native PHP type only (no phpdoc), so that
     * getPreferredPropertyType falls through to the $property->type branch.
     */
    public string $nativeTypedProp = 'hello';

    /**
     * Readonly property to exercise the is_readonly branch in formatPropertySignature.
     */
    public readonly int $readonlyProp;

    /**
     * Static property to exercise the is_static branch in formatPropertySignature.
     *
     * @var float
     */
    public static float $staticProp = 3.14;

    /**
     * Private property – must be filtered out when $access = ['public'].
     *
     * @var string
     */
    private string $privateProp = 'secret';

    /**
     * Property whose name starts with underscore – must be skipped when
     * $skipPropertiesWithLeadingUnderscore is true.
     *
     * @var int
     */
    public int $_underscoreProp = 0;

    /**
     * Property with only a default value and no type annotation at all.
     * This exercises the typeFromDefaultValue fallback in getPreferredPropertyType.
     */
    public $defaultOnlyProp = 'hello';

    public function __construct()
    {
        $this->readonlyProp = 42;
    }

    /**
     * Normal public method – always visible.
     *
     * @return string
     */
    public function normalMethod(): string
    {
        return 'ok';
    }

    /**
     * @deprecated Use normalMethod() instead.
     *
     * @return string
     */
    public function deprecatedMethod(): string
    {
        return 'old';
    }

    /**
     * Method whose name begins with underscore – filtered when
     * $skipMethodsWithLeadingUnderscore is true.
     *
     * @return void
     */
    public function _internalHelper(): void
    {
    }

    /**
     * Protected method – only visible when $access includes 'protected'.
     *
     * @return bool
     */
    protected function protectedHelper(): bool
    {
        return true;
    }

    // Method with absolutely no type information (no phpdoc, no return type,
    // no parameter type). Used to test todoModus=false code paths.
    public function noTypeInfo($param)
    {
        return $param;
    }
}
