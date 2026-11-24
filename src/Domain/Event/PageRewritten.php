<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Event;

use AlexandreBulete\DddFoundation\Domain\Event\DomainEvent;

/**
 * A page's text changed.
 */
final readonly class PageRewritten implements DomainEvent
{
    public function __construct(
        public string $pageId,
    ) {}
}
