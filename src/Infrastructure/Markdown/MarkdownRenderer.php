<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;

/**
 * Page bodies to HTML. The author writes Markdown, not HTML: raw HTML is
 * escaped and unsafe links (javascript:, data:) are dropped, so the output can
 * be printed as is. Rendered on read: a change of renderer never leaves stale
 * HTML in the database.
 */
final class MarkdownRenderer
{
    private readonly MarkdownParser $parser;
    private readonly HtmlRenderer $renderer;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->parser = new MarkdownParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }

    public function toHtml(string $markdown): string
    {
        return (string) $this->renderer->renderDocument($this->parser->parse($markdown));
    }
}
