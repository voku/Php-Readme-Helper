<?php declare(strict_types=1);

namespace voku\PhpReadmeHelper;

class GenerateStringHelper
{
    /**
     * Replaces $search from the beginning of string with $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $search      <p>The string to search for.</p>
     * @param string $replacement <p>The replacement.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string after the replacements.</p>
     */
    public static function str_replace_beginning(
        string $str,
        string $search,
        string $replacement
    ): string {
        if ($str === '') {
            if ($replacement === '') {
                return '';
            }

            if ($search === '') {
                return $replacement;
            }
        }

        if ($search === '') {
            return $str . $replacement;
        }

        if (\strpos($str, $search) === 0) {
            return $replacement . \substr($str, \strlen($search));
        }

        return $str;
    }

    /**
     * Replaces all occurrences of $search in $str by $replacement.
     *
     * @param string $str            <p>The input string.</p>
     * @param string $search         <p>The needle to search for.</p>
     * @param string $replacement    <p>The string to replace with.</p>
     * @param bool   $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with replaced parts.</p>
     */
    public static function replace(
        string $str,
        string $search,
        string $replacement,
        bool $case_sensitive = true
    ): string {
        if ($case_sensitive) {
            return \str_replace($search, $replacement, $str);
        }

        return \str_ireplace($search, $replacement, $str);
    }

    /**
     * Create a valid CSS identifier for e.g. "class"- or "id"-attributes.
     *
     * EXAMPLE: <code>UTF8::css_identifier('123foo/bar!!!'); // _23foo-bar</code>
     *
     * copy&past from https://github.com/drupal/core/blob/8.8.x/lib/Drupal/Component/Utility/Html.php#L95
     *
     * @param string   $str         <p>INFO: if no identifier is given e.g. " " or "", we will create a unique string automatically</p>
     * @param string[] $filter
     * @param bool     $stripe_tags
     * @param bool     $strtolower
     *
     * @psalm-pure
     *
     * @return string
     *
     * @psalm-param array<string,string> $filter
     */
    public static function css_identifier(
        string $str = '',
        array $filter = [
            ' ' => '-',
            '/' => '-',
            '[' => '',
            ']' => '',
        ],
        bool $stripe_tags = false,
        bool $strtolower = true
    ): string {
        // We could also use strtr() here but its much slower than str_replace(). In
        // order to keep '__' to stay '__' we first replace it with a different
        // placeholder after checking that it is not defined as a filter.
        $double_underscore_replacements = 0;

        // Fallback ...
        if (\trim($str) === '') {
            $str = \uniqid('auto-generated-css-class', true);
        }

        if ($stripe_tags) {
            $str = \strip_tags($str);
        }

        if ($strtolower) {
            $str = \strtolower($str);
        }

        if (!isset($filter['__'])) {
            $str = \str_replace('__', '##', $str, $double_underscore_replacements);
        }

        /* @noinspection ArrayValuesMissUseInspection */
        $str = \str_replace(\array_keys($filter), \array_values($filter), $str);
        // Replace temporary placeholder '##' with '__' only if the original
        // $identifier contained '__'.
        if ($double_underscore_replacements > 0) {
            $str = \str_replace('##', '__', $str);
        }

        // Valid characters in a CSS identifier are:
        // - the hyphen (U+002D)
        // - a-z (U+0030 - U+0039)
        // - A-Z (U+0041 - U+005A)
        // - the underscore (U+005F)
        // - 0-9 (U+0061 - U+007A)
        // - ISO 10646 characters U+00A1 and higher
        // We strip out any character not in the above list.
        $str = (string) \preg_replace('/[^\x{002D}\x{0030}-\x{0039}\x{0041}-\x{005A}\x{005F}\x{0061}-\x{007A}\x{00A1}-\x{FFFF}]/u', '', $str);
        // Identifiers cannot start with a digit, two hyphens, or a hyphen followed by a digit.
        $str = (string) \preg_replace(['/^[0-9]/', '/^(-[0-9])|^(--)/'], ['_', '__'], $str);

        return \trim($str, '-');
    }
}
