<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Twig;

use AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPages\FindPublishedPagesQuery;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Infrastructure\Markdown\MarkdownRenderer;
use AlexandreBulete\DddFoundation\Application\Query\QueryBusInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * `page.body|page_markdown` renders a page; `published_pages()` lists the
 * pages online, for a footer — asked once per request.
 */
final class PageExtension extends AbstractExtension
{
    /** @var list<Page>|null */
    private ?array $published = null;

    public function __construct(
        private readonly MarkdownRenderer $markdown,
        private readonly QueryBusInterface $queryBus,
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('page_markdown', $this->markdown->toHtml(...), ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('published_pages', $this->publishedPages(...)),
        ];
    }

    /**
     * @return list<Page>
     */
    public function publishedPages(): array
    {
        if ($this->published === null) {
            /** @var list<Page> $pages */
            $pages = $this->queryBus->ask(new FindPublishedPagesQuery());
            $this->published = $pages;
        }

        return $this->published;
    }
}
