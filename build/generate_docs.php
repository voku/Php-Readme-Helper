<?php

// Hint: I would recommend to put another composer.json in the build directory,
// so that this does not conflict with project dependencies.

require __DIR__ . '/../vendor/autoload.php';

$readmeGenerator = new \voku\PhpReadmeHelper\GenerateApi();

$readmeGenerator->hideTheFunctionIndex = true;

$readmeGenerator->templateMethod = <<<RAW
#### %name%
<a href="#voku-php-readme-class-methods">↑</a>
%description%

**Parameters:**
%params%

**Return:**
%return%

--------
RAW;

$readmeText = ($readmeGenerator)->generate(
    __DIR__ . '/../src/voku/PhpReadmeHelper/GenerateApi.php',
    __DIR__ . '/docs/base.md'
);

file_put_contents(__DIR__ . '/../README.md', $readmeText);
