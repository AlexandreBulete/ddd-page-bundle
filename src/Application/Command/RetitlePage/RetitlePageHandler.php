<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\RetitlePage;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use AlexandreBulete\DddFoundation\Domain\Exception\EntityNotFoundException;
use Psr\Clock\ClockInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;

#[AsCommandHandler]
final readonly class RetitlePageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
        private ClockInterface $clock,
    ) {}

    public function __invoke(RetitlePageCommand $command): Page
    {
        $page = $this->pages->findById($command->id)
            ?? throw new EntityNotFoundException(Page::class, $command->id);

        $page->retitle($command->title, $this->clock->now());
        $this->pages->save($page);

        return $page;
    }
}
