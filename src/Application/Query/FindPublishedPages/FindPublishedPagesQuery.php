<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPages;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddFoundation\Application\Authorization\PublicAccess;
use AlexandreBulete\DddFoundation\Application\Query\QueryInterface;

/**
 * Every page online, by title — of one placement when given: a footer, a
 * menu, a sitemap.
 *
 * @implements QueryInterface<list<Page>>
 */
#[PublicAccess]
final readonly class FindPublishedPagesQuery implements QueryInterface
{
    public function __construct(
        public ?Placement $placement = null,
    ) {}
}
