<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Event;

use AlexandreBulete\DddFoundation\Domain\Event\DomainEvent;

/**
 * A page moved: links to the previous slug no longer reach it.
 */
final readonly class PageSlugChanged implements DomainEvent
{
    public function __construct(
        public string $pageId,
        public string $slug,
        public string $previousSlug,
    ) {}
}
