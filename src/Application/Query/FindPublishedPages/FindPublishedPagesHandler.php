<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPages;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddFoundation\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindPublishedPagesHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
    ) {}

    /**
     * @return list<Page>
     */
    public function __invoke(FindPublishedPagesQuery $query): array
    {
        return $this->pages->published($query->placement);
    }
}
