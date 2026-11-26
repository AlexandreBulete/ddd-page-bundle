<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Model;

use AlexandreBulete\DddPageBundle\Domain\Event\PageCreated;
use AlexandreBulete\DddPageBundle\Domain\Event\PageDeleted;
use AlexandreBulete\DddPageBundle\Domain\Event\PagePlaced;
use AlexandreBulete\DddPageBundle\Domain\Event\PagePublished;
use AlexandreBulete\DddPageBundle\Domain\Event\PageRetitled;
use AlexandreBulete\DddPageBundle\Domain\Event\PageRewritten;
use AlexandreBulete\DddPageBundle\Domain\Event\PageSlugChanged;
use AlexandreBulete\DddPageBundle\Domain\Event\PageUnpublished;
use AlexandreBulete\DddPageBundle\Domain\Exception\EmptyPageException;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddFoundation\Domain\Model\RecordsEvents;

/**
 * A standalone page of a site — legal notice, privacy policy, a "how I
 * work" — written in Markdown, online or not.
 *
 * No date, no author, no category: what changes over time is the text, and
 * the date it last changed, which a legal page has to show. Its placement
 * tells the site where to link it (footer, menu, a reserved spot), or
 * nowhere: reachable by its address only.
 */
final class Page
{
    use RecordsEvents;

    public const int TITLE_MAX_LENGTH = 200;

    private function __construct(
        private(set) PageId $id,
        private(set) string $title,
        private(set) Slug $slug,
        private(set) string $body,
        private(set) bool $published,
        private(set) \DateTimeImmutable $createdAt,
        private(set) \DateTimeImmutable $updatedAt,
        private(set) ?Placement $placement = null,
    ) {}

    public static function create(PageId $id, string $title, Slug $slug, string $body, \DateTimeImmutable $at): self
    {
        $page = new self($id, self::assertTitle($title), $slug, $body, false, $at, $at);
        $page->recordEvent(new PageCreated((string) $id, $slug->value()));

        return $page;
    }

    public function retitle(string $title, \DateTimeImmutable $at): void
    {
        $title = self::assertTitle($title);
        if ($title === $this->title) {
            return;
        }

        $this->title = $title;
        $this->updatedAt = $at;
        $this->recordEvent(new PageRetitled((string) $this->id));
    }

    /**
     * The use case checks that no other page holds the slug.
     */
    public function changeSlug(Slug $slug, \DateTimeImmutable $at): void
    {
        if ($slug->equals($this->slug)) {
            return;
        }

        $previous = $this->slug;
        $this->slug = $slug;
        $this->updatedAt = $at;
        $this->recordEvent(new PageSlugChanged((string) $this->id, $slug->value(), $previous->value()));
    }

    public function rewrite(string $body, \DateTimeImmutable $at): void
    {
        if ($body === $this->body) {
            return;
        }
        if ($this->published && trim($body) === '') {
            throw new EmptyPageException('A page online cannot be emptied: take it offline first.');
        }

        $this->body = $body;
        $this->updatedAt = $at;
        $this->recordEvent(new PageRewritten((string) $this->id));
    }

    /**
     * Where the site links the page; null for nowhere. Not a change of text:
     * the "last updated" date stays. The use case checks the placement
     * exists, and that an exclusive one is free.
     */
    public function place(?Placement $placement): void
    {
        if ($placement === null ? $this->placement === null : ($this->placement !== null && $placement->equals($this->placement))) {
            return;
        }

        $previous = $this->placement;
        $this->placement = $placement;
        $this->recordEvent(new PagePlaced((string) $this->id, $placement?->value(), $previous?->value()));
    }

    public function publish(\DateTimeImmutable $at): void
    {
        if ($this->published) {
            return;
        }
        if (trim($this->body) === '') {
            throw new EmptyPageException('An empty page cannot go online.');
        }

        $this->published = true;
        $this->recordEvent(new PagePublished((string) $this->id, $this->slug->value()));
    }

    public function unpublish(\DateTimeImmutable $at): void
    {
        if (!$this->published) {
            return;
        }

        $this->published = false;
        $this->recordEvent(new PageUnpublished((string) $this->id));
    }

    public function delete(): void
    {
        $this->recordEvent(new PageDeleted((string) $this->id, $this->slug->value()));
    }

    private static function assertTitle(string $title): string
    {
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > self::TITLE_MAX_LENGTH) {
            throw new \InvalidArgumentException(sprintf('A page title is 1 to %d characters long.', self::TITLE_MAX_LENGTH));
        }

        return $title;
    }
}
