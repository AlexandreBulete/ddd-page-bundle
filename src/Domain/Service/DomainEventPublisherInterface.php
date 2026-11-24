<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Service;

/**
 * Hands the events an aggregate recorded to whoever listens, once it is saved.
 */
interface DomainEventPublisherInterface
{
    /**
     * @param iterable<object> $events
     */
    public function publishAll(iterable $events): void;
}
