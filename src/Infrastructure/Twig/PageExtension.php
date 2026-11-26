<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Twig;

use AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPages\FindPublishedPagesQuery;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddPageBundle\Infrastructure\Markdown\MarkdownRenderer;
use AlexandreBulete\DddFoundation\Application\Query\QueryBusInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * `page.body|page_markdown` renders a page; `published_pages('legal')` lists
 * the pages online of a placement (all of them without one), for a footer or
 * a menu; `placed_page('about')` is the page online of an exclusive
 * placement, or null. Each list is asked once per request.
 */
final class PageExtension extends AbstractExtension
{
    /** @var array<string, list<Page>> */
    private array $published = [];

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
            new TwigFunction('placed_page', $this->placedPage(...)),
        ];
    }

    /**
     * @return list<Page>
     */
    public function publishedPages(?string $placement = null): array
    {
        $key = $placement ?? '';
        if (!isset($this->published[$key])) {
            /** @var list<Page> $pages */
            $pages = $this->queryBus->ask(new FindPublishedPagesQuery($placement === null ? null : Placement::fromString($placement)));
            $this->published[$key] = $pages;
        }

        return $this->published[$key];
    }

    public function placedPage(string $placement): ?Page
    {
        return $this->publishedPages($placement)[0] ?? null;
    }
}
