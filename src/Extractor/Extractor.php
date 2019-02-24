<?php

declare(strict_types=1);

namespace DocCov\Extractor;

use Generator;

interface Extractor
{
    public function extract(string $content): Generator;
}
