<?php

declare(strict_types=1);

namespace DocCov\Reflection;

use Composer\Autoload\ClassLoader;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class ComposerSourceLocatorFactory implements SourceLocatorFactory
{
    /** @var ClassLoader */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function forPaths(array $paths): SourceLocator
    {
        $astLocator = (new BetterReflection())->astLocator();

        return new AggregateSourceLocator([
            new DirectoriesSourceLocator($paths, $astLocator),
            new ComposerSourceLocator($this->classLoader, $astLocator),
        ]);
    }
}