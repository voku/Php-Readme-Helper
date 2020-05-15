<?php declare(strict_types=1);

namespace voku\PhpReadmeHelper;

use voku\PhpReadmeHelper\Template\TemplateFormatter;
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
     * @var string
     */
    public $templateMethod = <<<RAW
## %name%
<a href="#class-methods">↑</a>
%description%

**Parameters:**
%params%

**Return:**
- `%return%`

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
     * 'public' || 'protected' || 'private'
     *
     * @var string[]
     */
    public $access = ['public'];

    /**
     * @param string        $codePath
     * @param string        $baseDocFilePath
     * @param string[]|null $useClasses
     *
     * @return string
     *
     * @noinspection NestedTernaryOperatorInspection
     */
    public function generate(
        string $codePath,
        string $baseDocFilePath,
        array $useClasses = null
    ): string {
        $phpFiles = PhpCodeParser::getPhpFiles($codePath);
        $phpClasses = $phpFiles->getClasses();

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

            $functionsDocumentation = [];
            $functionsIndex = [];

            foreach ($phpClass->methods as $method) {

                /** @noinspection InArrayMissUseInspection */
                if (!\in_array($method->access, $this->access, true)) {
                    continue;
                }

                if ($this->skipDeprecatedMethods && $method->is_deprecated) {
                    continue;
                }

                if ($this->skipMethodsWithLeadingUnderscore && \strpos($method->name, '_') === 0) {
                    continue;
                }

                $methodIndexTemplate = new TemplateFormatter($this->templateIndexLink);

                $methodTemplate = new TemplateFormatter($this->templateMethod);

                // -- params
                $params = [];
                $paramsTypes = [];
                foreach ($method->parameters as $param) {
                    $paramsTemplate = new TemplateFormatter($this->templateMethodParam);
                    $paramsTemplate->set(
                        'param',
                        (
                            $param->typeFromPhpDocPslam
                                ?: $param->typeFromPhpDoc
                                ?: $param->type
                                ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
                        ) . (
                            GenerateStringHelper::str_replace_beginning($param->typeMaybeWithComment, $param->typeFromPhpDoc, '')
                                ?: ' ' . '$' . $param->name
                        )
                    );
                    $params[] = $paramsTemplate->format();

                    $paramsTypes[] = (
                        $param->typeFromPhpDocSimple
                            ?: $param->typeFromPhpDoc
                            ?: $param->type
                            ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
                    ) . ' ' . '$' . $param->name;
                }

                if (\count($params) !== 0) {
                    $methodTemplate->set('params', \implode("\n", $params));
                } else {
                    $methodTemplate->set('params', '__nothing__');
                }

                // -- return

                $methodWithType = $method->name . '(' . \trim(\implode(', ', $paramsTypes)) . '): ' . $method->returnTypeFromPhpDoc;

                $description = \trim($method->summary . "\n\n" . $method->description);

                $methodTemplate->set('name', $methodWithType);
                $methodTemplate->set('description', $description);
                $methodTemplate->set(
                    'return',
                    $method->returnTypeMaybeWithComment
                        ?: $method->returnTypeFromPhpDocPslam
                        ?: $method->returnType
                        ?: ($this->todoModus ? 'TODO: __not_detected__' : '')
                );

                $methodIndexTemplate->set('title', $method->name);
                $methodIndexTemplate->set('href', '#' . GenerateStringHelper::css_identifier($methodWithType));

                $functionsDocumentation[$method->name] = $methodTemplate->format();
                $functionsIndex[$method->name] = $methodIndexTemplate->format();
            }

            \ksort($functionsDocumentation);
            $functionsDocumentation = \array_values($functionsDocumentation);

            \ksort($functionsIndex);

            // -------------------------------------

            $documentTemplate->set($functionListHelper, \implode("\n", $functionsDocumentation));

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
            $indexStrResult = '<table>' . $indexStrResult . '</table>';

            $documentTemplate->set($functionIndexHelper, $indexStrResult);
        }

        return $documentTemplate->format();
    }
}
