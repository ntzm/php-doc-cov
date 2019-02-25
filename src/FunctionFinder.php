<?php

declare(strict_types=1);

namespace DocCov;

use Composer\Autoload\ClassLoader;
use Generator;
use ReflectionMethod;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class FunctionFinder
{
    private const METHOD_NAMES_BLACKLIST = [
        '__construct' => true,
        '__destruct' => true,
    ];

    /** @var ClassLoader */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * @param string[] $paths
     * @return Generator<\Roave\BetterReflection\Reflection\ReflectionMethod>
     */
    public function find(array $paths): Generator
    {
        $astLocator = (new BetterReflection())->astLocator();

        $locator = new AggregateSourceLocator([
            new DirectoriesSourceLocator($paths, $astLocator),
            new ComposerSourceLocator($this->classLoader, $astLocator),
        ]);

        $reflector = new ClassReflector($locator);

        foreach ($reflector->getAllClasses() as $class) {
            if ($class->isInterface()) {
                continue;
            }

            if ($this->isInternal($class)) {
                continue;
            }

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (isset(self::METHOD_NAMES_BLACKLIST[$method->getName()])) {
                    continue;
                }

                yield $method;
            }
        }
    }

    private function isInternal(ReflectionClass $class): bool
    {
        return stripos($class->getDocComment(), '@internal') !== false;
    }
}
