<?php

declare(strict_types=1);

namespace DocCov\Reflection;

use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

interface SourceLocatorFactory
{
    public function forPaths(array $paths): SourceLocator;
}
