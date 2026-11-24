<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Event;

use AlexandreBulete\DddFoundation\Domain\Event\DomainEvent;

/**
 * A page was created, offline.
 */
final readonly class PageCreated implements DomainEvent
{
    public function __construct(
        public string $pageId,
        public string $slug,
    ) {}
}
