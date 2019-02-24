<?php

declare(strict_types=1);

namespace DocCov;

use DocCov\Coverage\Driver;

final class CodeRunner
{
    /** @var Driver */
    private $coverageDriver;

    public function __construct(Driver $coverageDriver)
    {
        $this->coverageDriver = $coverageDriver;
    }

    public function run(string $code): void
    {
        $this->coverageDriver->start();

        eval($code);

        $this->coverageDriver->pause();
    }
}
