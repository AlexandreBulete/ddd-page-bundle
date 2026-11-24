<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPage;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddFoundation\Application\Handler\QuerySingleHandler;
use AlexandreBulete\DddFoundation\Application\Query\AsQueryHandler;

/**
 * @extends QuerySingleHandler<Page>
 */
#[AsQueryHandler]
final readonly class FindPageHandler extends QuerySingleHandler
{
    public function __construct(PageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function __invoke(FindPageQuery $query): Page
    {
        return $this->build($query);
    }
}
