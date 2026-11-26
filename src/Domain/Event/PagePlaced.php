<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Event;

use AlexandreBulete\DddFoundation\Domain\Event\DomainEvent;

final readonly class PagePlaced implements DomainEvent
{
    public function __construct(
        public string $pageId,
        public ?string $placement,
        public ?string $previousPlacement,
    ) {}
}
