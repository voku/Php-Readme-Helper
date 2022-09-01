<?php declare(strict_types=1);

namespace voku\PhpReadmeHelper;

use voku\PhpReadmeHelper\Template\TemplateFormatter;
use voku\SimplePhpParser\Model\PHPMethod;
use voku\SimplePhpParser\Parsers\PhpCodeParser;

class GenerateApi
{
    /**
     * write T-O-D-O comment into the output
     *
     * @var bool
     */
    public $todoModus = true;

    /**
     * @var bool
     */
    public $skipDeprecatedMethods = true;

    /**
     * @var bool
     */
    public $skipMethodsWithLeadingUnderscore = true;

    /**
     * @var bool
     */
    public $hideTheFunctionIndex = false;

    /**
     * @var string
     */
    public $templateMethod = <<<RAW
## %name%
<a href="#voku-php-readme-class-methods">↑</a>
%description%

**Parameters:**
%params%

**Return:**
%return%

--------

RAW;

    /**
     * @var string
     */
    public $templateIndexLink = <<<RAW
<a href="%href%">%title%</a>
RAW;

    /**
     * @var string
     */
    public $templateMethodParam = <<<RAW
- `%param%`
RAW;

    /**
     * @var string
     */
    public $templateMethodReturn = <<<RAW
- `%return%`
RAW;

    /**
     * 'public' || 'protected' || 'private'
     *
     * @var string[]
     */
    public $access = ['public'];

    /**
     * This method can generate API documentation with types from php code into your README file.
     *
     * @param string        $codePath        <p>Path to a file or a directory.</p>
     * @param string        $baseDocFilePath <p>Path to your base file for the README.</p>
     * @param string[]|null $useClasses      <p>If used, you can specify to classes, you will use.</p>
     *
     * @return string
     *                <p>Return a string with the generated README.</p>
     */
    public function generate(
        string $codePath,
        string $baseDocFilePath,
        array $useClasses = null
    ): string {
        $phpFiles = PhpCodeParser::getPhpFiles($codePath);
        $phpClasses = \array_merge(
            $phpFiles->getClasses(),
            $phpFiles->getInterfaces()
        );

        // fallback
        if ($useClasses === null) {
            $useClasses = [];
            foreach ($phpClasses as $phpClass) {
                $useClasses[] = $phpClass->name;
            }
        }

        // -------------------------------------

        $templateDocument = \file_get_contents($baseDocFilePath);
        if (!$templateDocument) {
            throw new \Exception('Could not load file: "' . $baseDocFilePath . '"');
        }
        $documentTemplate = new TemplateFormatter($templateDocument);

        // -------------------------------------

        foreach ($useClasses as $useClass) {
            $functionListHelper = '__functions_list__' . $useClass . '__';
            if (\strpos($templateDocument, $functionListHelper) === false) {
                throw new \Exception('missing string: "%' . $functionListHelper . '%" in "' . $templateDocument . '"');
            }

            $functionIndexHelper = '__functions_index__' . $useClass . '__';
            if (\strpos($templateDocument, $functionIndexHelper) === false) {
                throw new \Exception('missing string: "%' . $functionIndexHelper . '%" in "' . $templateDocument . '"');
            }

            $phpClass = $phpClasses[$useClass];

            // reset
            $functionsDocumentation = [];
            $functionsIndex = [];

            foreach ($phpClass->methods as $method) {

                /** @noinspection InArrayMissUseInspection */
                if (!\in_array($method->access, $this->access, true)) {
                    continue;
                }

                if ($this->skipDeprecatedMethods && $method->hasDeprecatedTag) {
                    continue;
                }

                if ($this->skipMethodsWithLeadingUnderscore && \strpos($method->name, '_') === 0) {
                    continue;
                }

                $paramsInfo = $this->getMethodParams($method);

                $returnInto = $this->getMethodReturn($method);

                $methodWithType = $method->name . '(' . \trim(\implode(', ', $paramsInfo['paramsTypes'])) . '): ' . $method->returnTypeFromPhpDoc;

                // method --------------------------------------------------------------

                $methodTemplate = new TemplateFormatter($this->templateMethod);

                if (\count($paramsInfo['params']) > 0) {
                    $methodTemplate->set('params', \implode("\n", $paramsInfo['params']));
                } else {
                    $methodTemplate->set('params', '__nothing__');
                }

                $methodTemplate->set('return', $returnInto);
                $methodTemplate->set('name', $methodWithType);
                $methodTemplate->set('description', \trim($method->summary . "\n\n" . $method->description));

                $functionsDocumentation[$method->name] = $methodTemplate->format();

                // index --------------------------------------------------------------

                if ($this->hideTheFunctionIndex !== true) {
                    $methodIndexTemplate = new TemplateFormatter($this->templateIndexLink);
                    $methodIndexTemplate->set('title', $method->name);
                    $methodIndexTemplate->set('href', '#' . GenerateStringHelper::css_identifier($methodWithType));

                    $functionsIndex[$method->name] = $methodIndexTemplate->format();
                }
            }

            \ksort($functionsDocumentation, \SORT_NATURAL);
            \ksort($functionsIndex, \SORT_NATURAL);

            // --------------------------------------------------------------

            $documentTemplate->set($functionListHelper, \implode("\n", $functionsDocumentation));

            $indexStrResult = $this->getIndexHtmlTable($functionsIndex);
            $documentTemplate->set($functionIndexHelper, $indexStrResult);
        }

        return '[//]: # (AUTO-GENERATED BY "PHP README Helper": base file -> ' . \str_replace(\getcwd() . \DIRECTORY_SEPARATOR, '', $baseDocFilePath) . ')' . "\n" . $documentTemplate->format();
    }

    /**
     * @param \voku\SimplePhpParser\Model\PHPMethod $method
     *
     * @return string[][]
     *
     * @psalm-return array{$params: string[], paramsTypes: string[]}
     */
    private function getMethodParams(PHPMethod $method): array
    {
        // init
        $params = [];
        $paramsTypes = [];

        foreach ($method->parameters as $param) {
            $paramsTemplate = new TemplateFormatter($this->templateMethodParam);
            /** @noinspection NestedTernaryOperatorInspection */
            $paramsTemplate->set(
                'param',
                (
                    $param->typeFromPhpDocExtended
                        ?: $param->typeFromPhpDoc
                        ?: $param->type
                        ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
                )
                .
                (
                    GenerateStringHelper::str_replace_beginning(
                        (string) $param->typeFromPhpDocMaybeWithComment,
                        (string) $param->typeFromPhpDoc,
                        ''
                    ) ?: ' ' . '$' . $param->name
                )
            );

            $params[] = $paramsTemplate->format();

            /** @noinspection NestedTernaryOperatorInspection */
            $paramsTypes[] = (
                $param->typeFromPhpDocSimple
                     ?: $param->typeFromPhpDoc
                     ?: $param->type
                     ?: $param->typeFromPhpDocExtended
                     ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
            ) . ' ' . '$' . $param->name;
        }

        return ['params' => $params, 'paramsTypes' => $paramsTypes];
    }

    /**
     * @param string[] $functionsIndex
     *
     * @return string
     *
     * @psalm-param array<string, string> $functionsIndex
     */
    private function getIndexHtmlTable(array $functionsIndex): string
    {
        // init
        $indexLastChar = null;
        $indexStrResult = '';
        $counterTmp = 0;

        foreach ($functionsIndex as $_index => $_template) {
            ++$counterTmp;

            if ($counterTmp === 1) {
                $indexStrResult .= '<tr>';
            }

            $indexStrResult .= '<td>' . \sprintf("%s\n", $_template) . '</td>';

            if ($counterTmp === 4) {
                $counterTmp = 0;
                $indexStrResult .= '</tr>';
            }
        }

        if ($counterTmp > 0) {
            $indexStrResult .= '</tr>';
        }

        if ($indexStrResult) {
            $indexStrResult = '<table>' . $indexStrResult . '</table>';
        }

        return '<p id="voku-php-readme-class-methods"></p>' . $indexStrResult;
    }

    private function getMethodReturn(PHPMethod $method): string
    {
        $returnTemplate = new TemplateFormatter($this->templateMethodReturn);

        /** @noinspection NestedTernaryOperatorInspection */
        $returnTemplate->set(
            'return',
            $method->returnTypeFromPhpDocMaybeWithComment
                ?: $method->returnTypeFromPhpDocExtended
                ?: $method->returnType
                ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
        );

        return $returnTemplate->format();
    }
}
