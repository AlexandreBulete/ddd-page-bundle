<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Tests\Unit\Domain;

use AlexandreBulete\DddPageBundle\Domain\Event\PageCreated;
use AlexandreBulete\DddPageBundle\Domain\Event\PagePublished;
use AlexandreBulete\DddPageBundle\Domain\Event\PageSlugChanged;
use AlexandreBulete\DddPageBundle\Domain\Exception\EmptyPageException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PageTest extends TestCase
{
    private \DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->now = new \DateTimeImmutable('2026-11-24 10:00:00');
    }

    #[Test]
    public function a_new_page_is_offline(): void
    {
        $page = Page::create(PageId::generate(), ' Legal notice ', Slug::fromString('legal-notice'), 'Text.', $this->now);

        self::assertSame('Legal notice', $page->title);
        self::assertFalse($page->published);
        self::assertInstanceOf(PageCreated::class, $page->releaseEvents()[0]);
    }

    #[Test]
    public function an_empty_page_cannot_go_online_nor_be_emptied_once_online(): void
    {
        $page = Page::create(PageId::generate(), 'Privacy', Slug::fromString('privacy'), '  ', $this->now);
        try {
            $page->publish($this->now);
            self::fail('An empty page went online.');
        } catch (EmptyPageException) {
        }

        $page->rewrite('What we keep, and why.', $this->now);
        $page->publish($this->now);
        self::assertTrue($page->published);

        $this->expectException(EmptyPageException::class);
        $page->rewrite('', $this->now);
    }

    #[Test]
    public function only_changes_are_recorded_and_dated(): void
    {
        $page = Page::create(PageId::generate(), 'Privacy', Slug::fromString('privacy'), 'Text.', $this->now);
        $page->releaseEvents();
        $later = $this->now->modify('+1 day');

        $page->retitle('Privacy', $later);
        $page->rewrite('Text.', $later);
        self::assertEquals($this->now, $page->updatedAt);

        $page->changeSlug(Slug::fromString('privacy-policy'), $later);
        $page->publish($later);
        $page->publish($later);

        $events = $page->releaseEvents();
        self::assertCount(2, $events);
        self::assertInstanceOf(PageSlugChanged::class, $events[0]);
        self::assertSame('privacy', $events[0]->previousSlug);
        self::assertInstanceOf(PagePublished::class, $events[1]);
        self::assertEquals($later, $page->updatedAt);
    }

    #[Test]
    public function a_slug_is_lowercase_letters_digits_and_hyphens(): void
    {
        self::assertSame('legal-notice', Slug::fromString(' Legal-Notice ')->value());

        $this->expectException(\InvalidArgumentException::class);
        Slug::fromString('legal--notice');
    }
}
