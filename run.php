<?php

declare(strict_types=1);

use DocCov\CodeRunner;
use DocCov\Coverage\PcovDriver;
use DocCov\Extractor\MarkdownExtractor;
use DocCov\CodeTransformer;
use DocCov\FunctionFinder;

require __DIR__ . '/vendor/autoload.php';

$md = '

```php
$transformer = new DocCov\CodeTransformer();

$transformer->transform("foo");
```
';

$extractor = new MarkdownExtractor();
$transformer = new CodeTransformer();
$coverageDriver = new PcovDriver();
$runner = new CodeRunner($coverageDriver);

foreach ($extractor->extract($md) as $code) {
    $code = $transformer->transform($code);

    var_dump($code);

    $runner->run($code);
}

$coverage = $coverageDriver->collect();

$finder = new FunctionFinder();

$totalMethods = 0;
$totalCoveredMethods = 0;

/** @var \Roave\BetterReflection\Reflection\ReflectionMethod $method */
foreach ($finder->find(['src']) as $method) {
    ++$totalMethods;

    $isCovered = $coverage->hasCoveredMethod($method);

    if ($isCovered) {
        ++$totalCoveredMethods;
    }

    var_dump($method->getDeclaringClass()->getName() . '::' . $method->getName(), $isCovered);
}

$percentageCovered = number_format($totalCoveredMethods / $totalMethods * 100, 2);

var_dump($percentageCovered . '%');
