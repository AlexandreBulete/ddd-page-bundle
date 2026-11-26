<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\PlacePage;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use AlexandreBulete\DddFoundation\Domain\Exception\EntityNotFoundException;
use AlexandreBulete\DddPageBundle\Domain\Exception\PlacementTakenException;
use AlexandreBulete\DddPageBundle\Domain\Exception\UnknownPlacementException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\PlacementCatalogInterface;

#[AsCommandHandler]
final readonly class PlacePageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
        private PlacementCatalogInterface $placements,
    ) {}

    public function __invoke(PlacePageCommand $command): Page
    {
        $page = $this->pages->findById($command->id)
            ?? throw new EntityNotFoundException(Page::class, $command->id);

        $placement = $command->placement;
        if ($placement !== null) {
            if (!$this->placements->has($placement)) {
                throw new UnknownPlacementException($placement);
            }
            if ($this->placements->isExclusive($placement)) {
                foreach ($this->pages->placedAt($placement) as $holder) {
                    if (!$holder->id->equals($page->id)) {
                        throw new PlacementTakenException($placement, $holder->title);
                    }
                }
            }
        }

        $page->place($placement);
        $this->pages->save($page);

        return $page;
    }
}
