<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\RetitlePage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

/**
 * Gives a page another title.
 *
 * @implements CommandInterface<Page>
 */
final readonly class RetitlePageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
        public string $title,
    ) {}
}
