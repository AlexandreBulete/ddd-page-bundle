<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\RewritePage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

/**
 * Replaces a page's text.
 *
 * @implements CommandInterface<Page>
 */
final readonly class RewritePageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
        public string $body,
    ) {}
}
