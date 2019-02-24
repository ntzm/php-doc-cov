<?php

declare(strict_types=1);

namespace DocCov;

final class CodeTransformer
{
    public function transform(string $code): string
    {
        $code = trim($code);

        if (substr($code, 0, strlen('<?php')) === '<?php') {
            $code = substr($code, strlen('<?php'));
        }

        return $code;
    }
}
