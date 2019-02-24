<?php

declare(strict_types=1);

namespace DocCov\Coverage;

final class XdebugDriver implements Driver
{
    public function start(): void
    {
        xdebug_start_code_coverage();
    }

    public function pause(): void
    {
        xdebug_stop_code_coverage(0);
    }

    public function collect(): Coverage
    {
        $coverage = xdebug_get_code_coverage();

        xdebug_stop_code_coverage();

        return new Coverage($coverage);
    }
}
