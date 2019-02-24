<?php

declare(strict_types=1);

namespace DocCov\Extractor;

use Generator;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;

final class MarkdownExtractor implements Extractor
{
    public function extract(string $markdown): Generator
    {
        $parser = new DocParser(Environment::createCommonMarkEnvironment());

        $walker = $parser->parse($markdown)->walker();

        while ($event = $walker->next()) {
            if ($event->isEntering()) {
                continue;
            }

            $node = $event->getNode();

            if (! $node instanceof FencedCode) {
                continue;
            }

            if ($node->getInfo() !== 'php') {
                continue;
            }

            yield $node->getStringContent();
        }
    }
}
