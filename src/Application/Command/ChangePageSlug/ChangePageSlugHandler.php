<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\ChangePageSlug;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use AlexandreBulete\DddFoundation\Domain\Exception\EntityNotFoundException;
use Psr\Clock\ClockInterface;
use AlexandreBulete\DddPageBundle\Domain\Exception\SlugAlreadyUsedException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;

#[AsCommandHandler]
final readonly class ChangePageSlugHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
        private ClockInterface $clock,
    ) {}

    public function __invoke(ChangePageSlugCommand $command): Page
    {
        $page = $this->pages->findById($command->id)
            ?? throw new EntityNotFoundException(Page::class, $command->id);

        $holder = $this->pages->findBySlug($command->slug);
        if ($holder !== null && !$holder->id->equals($page->id)) {
            throw new SlugAlreadyUsedException($command->slug);
        }

        $page->changeSlug($command->slug, $this->clock->now());
        $this->pages->save($page);

        return $page;
    }
}
