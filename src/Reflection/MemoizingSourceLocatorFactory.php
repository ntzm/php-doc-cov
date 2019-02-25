<?php

declare(strict_types=1);

namespace DocCov\Reflection;

use Roave\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class MemoizingSourceLocatorFactory implements SourceLocatorFactory
{
    /** @var SourceLocatorFactory */
    private $wrappedSourceLocatorFactory;

    public function __construct(SourceLocatorFactory $wrappedSourceLocatorFactory)
    {
        $this->wrappedSourceLocatorFactory = $wrappedSourceLocatorFactory;
    }

    public function forPaths(array $paths): SourceLocator
    {
        return new MemoizingSourceLocator($this->wrappedSourceLocatorFactory->forPaths($paths));
    }
}