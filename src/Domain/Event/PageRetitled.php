<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Event;

use AlexandreBulete\DddFoundation\Domain\Event\DomainEvent;

/**
 * A page has a new title.
 */
final readonly class PageRetitled implements DomainEvent
{
    public function __construct(
        public string $pageId,
    ) {}
}
