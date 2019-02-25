<?php

declare(strict_types=1);

namespace DocCov;

use DocCov\Reflection\SourceLocatorFactory;
use Generator;
use ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;

final class MethodFinder
{
    private const METHOD_NAMES_BLACKLIST = [
        '__construct' => true,
        '__destruct' => true,
    ];

    /** @var SourceLocatorFactory */
    private $sourceLocatorFactory;

    public function __construct(SourceLocatorFactory $sourceLocatorFactory)
    {
        $this->sourceLocatorFactory = $sourceLocatorFactory;
    }

    /**
     * @param string[] $paths
     * @return Generator<\Roave\BetterReflection\Reflection\ReflectionMethod>
     */
    public function find(array $paths): Generator
    {
        $reflector = new ClassReflector($this->sourceLocatorFactory->forPaths($paths));

        foreach ($reflector->getAllClasses() as $class) {
            if ($class->isInterface()) {
                continue;
            }

            if ($this->isClassMarkedInternal($class)) {
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

    private function isClassMarkedInternal(ReflectionClass $class): bool
    {
        return stripos($class->getDocComment(), '@internal') !== false;
    }
}
