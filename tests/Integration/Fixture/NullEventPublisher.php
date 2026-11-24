<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Tests\Integration\Fixture;

use AlexandreBulete\DddPageBundle\Domain\Service\DomainEventPublisherInterface;

final class NullEventPublisher implements DomainEventPublisherInterface
{
    public function publishAll(iterable $events): void {}
}
