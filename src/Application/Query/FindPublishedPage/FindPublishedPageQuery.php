<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPage;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddFoundation\Application\Authorization\PublicAccess;
use AlexandreBulete\DddFoundation\Application\Query\QueryInterface;

/**
 * One page online. A page offline is not found: the public must not learn it
 * exists.
 *
 * @implements QueryInterface<Page>
 */
#[PublicAccess]
final readonly class FindPublishedPageQuery implements QueryInterface
{
    public function __construct(
        public Slug $slug,
    ) {}
}
