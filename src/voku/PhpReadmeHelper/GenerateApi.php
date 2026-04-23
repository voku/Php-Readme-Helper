<?php declare(strict_types=1);

namespace voku\PhpReadmeHelper;

use voku\PhpReadmeHelper\Template\TemplateFormatter;
use voku\SimplePhpParser\Model\PHPEnum;
use voku\SimplePhpParser\Model\PHPMethod;
use voku\SimplePhpParser\Model\PHPParameter;
use voku\SimplePhpParser\Model\PHPProperty;
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
     * @var bool
     */
    public $skipPropertiesWithLeadingUnderscore = true;

    /**
     * @var string
     */
    public $templateProperty = <<<RAW
## %name%
<a href="#voku-php-readme-class-properties">↑</a>
%description%

--------

RAW;

    /**
     * @var string
     */
    public $templateEnumCase = <<<RAW
- `%case%`
RAW;

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
        ?array $useClasses = null
    ): string {
        $phpFiles = PhpCodeParser::getPhpFiles($codePath);
        $phpClasses = \array_merge(
            $phpFiles->getClasses(),
            $phpFiles->getInterfaces(),
            $phpFiles->getEnums(),
            $phpFiles->getTraits()
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

                $methodModifiers = $method->is_static ? 'static ' : '';
                $methodWithType = $methodModifiers . $method->name . '(' . \trim(\implode(', ', $paramsInfo['paramsTypes'])) . '): ' . $this->getSignatureReturnType($method);

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

            // Optional: properties documentation
            $propertiesListHelper = '__properties_list__' . $useClass . '__';
            if (\strpos($templateDocument, $propertiesListHelper) !== false) {
                $propertiesDocumentation = [];
                $propertiesIndex = [];

                foreach ($phpClass->properties as $property) {
                    /** @noinspection InArrayMissUseInspection */
                    if (!\in_array($property->access, $this->access, true)) {
                        continue;
                    }

                    if ($this->skipPropertiesWithLeadingUnderscore && \strpos($property->name, '_') === 0) {
                        continue;
                    }

                    $type = $this->getPreferredPropertyType($property);
                    $signature = $this->formatPropertySignature($property, $type);

                    $propTemplate = new TemplateFormatter($this->templateProperty);
                    $propTemplate->set('name', $signature);
                    $propTemplate->set('description', $this->formatPropertyDescription($property, $type));

                    $propertiesDocumentation[$property->name] = $propTemplate->format();

                    $propIndexTemplate = new TemplateFormatter($this->templateIndexLink);
                    $propIndexTemplate->set('title', $property->name);
                    $propIndexTemplate->set('href', '#' . GenerateStringHelper::css_identifier($signature));

                    $propertiesIndex[$property->name] = $propIndexTemplate->format();
                }

                \ksort($propertiesDocumentation, \SORT_NATURAL);
                \ksort($propertiesIndex, \SORT_NATURAL);

                $documentTemplate->set($propertiesListHelper, \implode("\n", $propertiesDocumentation));
                $documentTemplate->set('__properties_index__' . $useClass . '__', $this->getIndexHtmlTable($propertiesIndex, 'voku-php-readme-class-properties'));
            }

            // Optional: enum cases documentation
            $enumCasesHelper = '__enum_cases__' . $useClass . '__';
            if ($phpClass instanceof PHPEnum && \strpos($templateDocument, $enumCasesHelper) !== false) {
                $documentTemplate->set($enumCasesHelper, $this->getEnumCasesOutput($phpClass));
            }
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
            $type = $this->getPreferredParamType($param);

            $paramsTemplate->set('param', $this->formatParam($param, $type));

            $params[] = $paramsTemplate->format();

            $paramsTypes[] = $type !== ''
                ? $type . ' ' . '$' . $param->name
                : '$' . $param->name;
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
    private function getIndexHtmlTable(array $functionsIndex, string $anchorId = 'voku-php-readme-class-methods'): string
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

        return '<p id="' . $anchorId . '"></p>' . $indexStrResult;
    }

    private function getMethodReturn(PHPMethod $method): string
    {
        $returnTemplate = new TemplateFormatter($this->templateMethodReturn);

        $returnTemplate->set('return', $this->formatReturn($method));

        return $returnTemplate->format();
    }

    private function formatParam(PHPParameter $param, string $type): string
    {
        $paramString = GenerateStringHelper::str_replace_beginning(
            (string) $param->typeFromPhpDocMaybeWithComment,
            $type,
            ''
        );

        if ($paramString !== (string) $param->typeFromPhpDocMaybeWithComment) {
            $paramString = $this->normalizeInlineWhitespace($paramString);

            if ($paramString !== '') {
                return \trim($type . ' ' . $paramString);
            }
        }

        if ($type === '') {
            return '$' . $param->name;
        }

        return $type . ' ' . '$' . $param->name;
    }

    private function formatReturn(PHPMethod $method): string
    {
        $type = $this->getPreferredReturnType($method);

        if ($type === '') {
            return '';
        }

        $returnString = (string) $method->returnTypeFromPhpDocMaybeWithComment;
        $returnSuffix = GenerateStringHelper::str_replace_beginning($returnString, $type, '');

        if ($returnString !== '' && $returnSuffix !== $returnString) {
            $returnSuffix = $this->normalizeInlineWhitespace($returnSuffix);

            if ($returnSuffix !== '') {
                return \trim($type . ' ' . $returnSuffix);
            }
        }

        return $type;
    }

    private function getPreferredParamType(PHPParameter $param): string
    {
        /** @noinspection NestedTernaryOperatorInspection */
        return $param->typeFromPhpDoc
            ?: $param->type
            ?: $param->typeFromPhpDocExtended
            ?: ($this->todoModus ? 'TODO: __not_detected__' : '');
    }

    private function getPreferredReturnType(PHPMethod $method): string
    {
        /** @noinspection NestedTernaryOperatorInspection */
        return $method->returnTypeFromPhpDoc
            ?: $method->returnType
            ?: $method->returnTypeFromPhpDocExtended
            ?: ($this->todoModus ? 'TODO: __not_detected__' : '');
    }

    private function getSignatureReturnType(PHPMethod $method): string
    {
        /** @noinspection NestedTernaryOperatorInspection */
        return $method->returnTypeFromPhpDoc
            ?: $method->returnType
            ?: $method->returnTypeFromPhpDocExtended
            ?: '';
    }

    private function getPreferredPropertyType(PHPProperty $property): string
    {
        /** @noinspection NestedTernaryOperatorInspection */
        return $property->typeFromPhpDocExtended
            ?: $property->type
            ?: $property->typeFromPhpDoc
            ?: $property->typeFromDefaultValue
            ?: ($this->todoModus ? 'TODO: __not_detected__' : '');
    }

    private function formatPropertySignature(PHPProperty $property, string $type): string
    {
        $parts = [];

        if ($property->access) {
            $parts[] = $property->access;
        }

        if ($property->is_readonly) {
            $parts[] = 'readonly';
        }

        if ($property->is_static) {
            $parts[] = 'static';
        }

        if ($type !== '') {
            $parts[] = $type;
        }

        $parts[] = '$' . $property->name;

        return \implode(' ', $parts);
    }

    private function formatPropertyDescription(PHPProperty $property, string $type): string
    {
        $fullDoc = (string) $property->typeFromPhpDocMaybeWithComment;

        $suffix = GenerateStringHelper::str_replace_beginning($fullDoc, $type, '');
        if ($suffix === $fullDoc) {
            return '';
        }

        $suffix = $this->normalizeInlineWhitespace($suffix);

        // Strip a leading variable name like "$foo" if present
        $suffix = (string) \preg_replace('/^\s*\$\w+\s*/', '', $suffix);

        return \trim($suffix);
    }

    private function getEnumCasesOutput(PHPEnum $phpClass): string
    {
        $cases = [];

        foreach ($phpClass->cases as $caseName => $caseValue) {
            $caseTemplate = new TemplateFormatter($this->templateEnumCase);

            $caseStr = $caseValue !== null
                ? $caseName . ' = ' . \var_export($caseValue, true)
                : $caseName;

            $caseTemplate->set('case', $caseStr);
            $cases[] = $caseTemplate->format();
        }

        return \implode("\n", $cases);
    }

    private function normalizeInlineWhitespace(string $value): string
    {
        return (string) \preg_replace('/\s+/u', ' ', \trim($value));
    }
}
