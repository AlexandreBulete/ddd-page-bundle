<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\ChangePageSlug;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;

/**
 * Moves a page to another address.
 *
 * @implements CommandInterface<Page>
 */
final readonly class ChangePageSlugCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
        public Slug $slug,
    ) {}
}
