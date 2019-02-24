<?php

declare(strict_types=1);

namespace DocCov;

use Generator;
use ReflectionMethod;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class FunctionFinder
{
    /**
     * @param string[] $paths
     * @return Generator<\Roave\BetterReflection\Reflection\ReflectionMethod>
     */
    public function find(array $paths): Generator
    {
        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new ClassReflector(new DirectoriesSourceLocator($paths, $astLocator));

        foreach ($reflector->getAllClasses() as $class) {
            if ($class->isInterface()) {
                continue;
            }

            if ($this->isInternal($class)) {
                continue;
            }

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getName() === '__construct') {
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
