<?php

declare(strict_types=1);

namespace DocCov\Coverage;

final class PcovDriver implements Driver
{
    public function start(): void
    {
        \pcov\start();
    }

    public function pause(): void
    {
        \pcov\stop();
    }

    public function collect(): Coverage
    {
        $waiting = \pcov\waiting();
        $collect = [];

        if ($waiting !== []) {
            $collect = \pcov\collect(\pcov\inclusive, $waiting);
        }

        if ($collect !== []) {
            \pcov\clear();
        }

        return new Coverage($collect);
    }
}
