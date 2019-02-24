<?php

declare(strict_types=1);

namespace DocCov\Coverage;

interface Driver
{
    public function start(): void;

    public function pause(): void;

    public function collect(): Coverage;
}
