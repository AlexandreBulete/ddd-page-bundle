<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPage;

use AlexandreBulete\DddPageBundle\Domain\Exception\PageNotFoundException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddFoundation\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindPublishedPageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
    ) {}

    public function __invoke(FindPublishedPageQuery $query): Page
    {
        $page = $this->pages->findBySlug($query->slug);
        if ($page === null || !$page->published) {
            throw new PageNotFoundException($query->slug);
        }

        return $page;
    }
}
