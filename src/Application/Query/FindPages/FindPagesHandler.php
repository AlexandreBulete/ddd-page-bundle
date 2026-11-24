<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPages;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddFoundation\Application\Handler\QueryCollectionHandler;
use AlexandreBulete\DddFoundation\Application\Query\AsQueryHandler;
use AlexandreBulete\DddFoundation\Domain\Repository\RepositoryInterface;

/**
 * @extends QueryCollectionHandler<Page>
 */
#[AsQueryHandler]
final readonly class FindPagesHandler extends QueryCollectionHandler
{
    public function __construct(PageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @return RepositoryInterface<Page>
     */
    public function __invoke(FindPagesQuery $query): RepositoryInterface
    {
        return $this->build($query);
    }
}
