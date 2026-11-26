<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Grid;

use AlexandreBulete\DddPageBundle\Application\Query\FindPages\FindPagesQuery;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Infrastructure\Config\ConfiguredPlacementCatalog;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddFoundation\Application\Query\QueryBusInterface;
use AlexandreBulete\DddSyliusBundle\Grid\GridPageResolver;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

final readonly class PageGridProvider implements DataProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ConfiguredPlacementCatalog $placements,
    ) {}

    /**
     * @return PagerfantaInterface<PageResource>
     */
    public function getData(Grid $grid, Parameters $parameters): PagerfantaInterface
    {
        /** @var array<string, string> $sorting */
        $sorting = $parameters->get('sorting', $grid->getSorting());

        /** @var PageRepositoryInterface $models */
        $models = $this->queryBus->ask(new FindPagesQuery(
            page: GridPageResolver::getCurrentPage($grid, $parameters),
            itemsPerPage: GridPageResolver::getItemsPerPage($grid, $parameters),
            withSorting: $sorting,
        ));

        $data = [];
        foreach ($models as $model) {
            /** @var Page $model */
            $placement = $model->placement?->value();
            $data[] = PageResource::fromModel($model, $placement === null ? null : ($this->placements->label($placement) ?? $placement));
        }

        return new Pagerfanta(new FixedAdapter($models->count(), $data));
    }
}
