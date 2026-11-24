<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\RewritePage;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use AlexandreBulete\DddFoundation\Domain\Exception\EntityNotFoundException;
use Psr\Clock\ClockInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;

#[AsCommandHandler]
final readonly class RewritePageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
        private ClockInterface $clock,
    ) {}

    public function __invoke(RewritePageCommand $command): Page
    {
        $page = $this->pages->findById($command->id)
            ?? throw new EntityNotFoundException(Page::class, $command->id);

        $page->rewrite($command->body, $this->clock->now());
        $this->pages->save($page);

        return $page;
    }
}
