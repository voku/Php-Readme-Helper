<?php declare(strict_types=1);

namespace voku\PhpReadmeHelper\Template;

use voku\PhpReadmeHelper\GenerateStringHelper;

class TemplateFormatter
{
    /**
     * @var array<string, string>
     */
    private $vars = [];

    /**
     * @var string
     */
    private $template;

    /**
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $var
     * @param string $value
     *
     * @return $this
     */
    public function set(string $var, string $value): self
    {
        $this->vars[$var] = $value;

        return $this;
    }

    /**
     * @param string $var
     * @param string $value
     *
     * @return $this
     */
    public function append(string $var, string $value): self
    {
        $this->vars[$var] = ($this->vars[$var] ?? '') . $value;

        return $this;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $s = $this->template;

        foreach ($this->vars as $name => $value) {
            $s = GenerateStringHelper::replace($s, \sprintf('%%%s%%', $name), $value);
        }

        return $s;
    }
}
