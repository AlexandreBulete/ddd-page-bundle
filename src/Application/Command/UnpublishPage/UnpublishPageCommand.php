<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\UnpublishPage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

/**
 * Takes a page offline.
 *
 * @implements CommandInterface<Page>
 */
final readonly class UnpublishPageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
    ) {}
}
