<?php

declare(strict_types=1);

namespace DocCov\Command;

use Composer\Autoload\ClassLoader;
use DocCov\CodeRunner;
use DocCov\CodeTransformer;
use DocCov\Coverage\Driver;
use DocCov\Coverage\PcovDriver;
use DocCov\Coverage\XdebugDriver;
use DocCov\Extractor\MarkdownExtractor;
use DocCov\MethodFinder;
use DocCov\Reflection\ComposerSourceLocatorFactory;
use DocCov\Reflection\MemoizingSourceLocatorFactory;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

final class Check extends Command
{
    /** @var ClassLoader */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        parent::__construct('check');

        $this->classLoader = $classLoader;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED);
    }

    protected function execute(Input $input, Output $output)
    {
        $md = '

```php
$transformer = new DocCov\CodeTransformer();

$transformer->transform("foo");
```
';

        $extractor = new MarkdownExtractor();
        $transformer = new CodeTransformer();
        $coverageDriver = $this->getCoverageDriver();
        $runner = new CodeRunner($coverageDriver);

        foreach ($extractor->extract($md) as $code) {
            $code = $transformer->transform($code);

            $runner->run($code);
        }

        $coverage = $coverageDriver->collect();

        $sourceLocatorFactory = new MemoizingSourceLocatorFactory(new ComposerSourceLocatorFactory($this->classLoader));

        $finder = new MethodFinder($sourceLocatorFactory);

        $totalMethods = 0;
        $totalCoveredMethods = 0;

        /** @var string[] $paths */
        $paths = $input->getArgument('paths');

        /** @var ReflectionMethod $method */
        foreach ($finder->find($paths) as $method) {
            ++$totalMethods;

            $isCovered = $coverage->hasCoveredMethod($method);

            if ($isCovered) {
                ++$totalCoveredMethods;
            }

            $output->writeln(($isCovered ? '✔ ' : '✖ ') . $method->getDeclaringClass()->getName() . '::' . $method->getName() . ($isCovered ? ' is covered' : ' is not covered'));
        }

        if ($totalMethods === 0) {
            $percentageCovered = '0.00%';
        } else {
            $percentageCovered = number_format($totalCoveredMethods / $totalMethods * 100, 2);
        }

        $output->writeln('Total coverage: ' . $percentageCovered . '%');
    }

    private function getCoverageDriver(): Driver
    {
        if (extension_loaded('pcov') && ini_get('pcov.enabled')) {
            return new PcovDriver();
        }

        if (extension_loaded('xdebug')) {
            return new XdebugDriver();
        }

        throw new RuntimeException('No coverage driver available');
    }
}
