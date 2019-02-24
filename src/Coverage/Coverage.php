<?php

declare(strict_types=1);

namespace DocCov\Coverage;

use Roave\BetterReflection\Reflection\ReflectionMethod;

final class Coverage
{
    private const LINE_COVERED = 1;

    /** @var array */
    private $coverage;

    public function __construct(array $coverage)
    {
        $this->coverage = $coverage;
    }

    public function hasCoveredMethod(ReflectionMethod $method): bool
    {
        $file = $method->getFileName();

        if (! isset($this->coverage[$file])) {
            return false;
        }

        for ($line = $method->getStartLine(); $line < $method->getEndLine(); ++$line) {
            if (! isset($this->coverage[$file][$line])) {
                continue;
            }

            if ($this->coverage[$file][$line] === self::LINE_COVERED) {
                return true;
            }
        }

        return false;
    }
}
