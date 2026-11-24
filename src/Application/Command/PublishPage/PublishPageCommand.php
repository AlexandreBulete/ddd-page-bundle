<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\PublishPage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

/**
 * Puts a page online.
 *
 * @implements CommandInterface<Page>
 */
final readonly class PublishPageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
    ) {}
}
