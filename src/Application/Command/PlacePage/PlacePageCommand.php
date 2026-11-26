<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\PlacePage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

/**
 * Links a page from a placement of the site, or from nowhere (null).
 *
 * @implements CommandInterface<Page>
 */
final readonly class PlacePageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
        public ?Placement $placement,
    ) {}
}
