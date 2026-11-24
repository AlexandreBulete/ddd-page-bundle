<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\DeletePage;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use AlexandreBulete\DddFoundation\Domain\Exception\EntityNotFoundException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;

#[AsCommandHandler]
final readonly class DeletePageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
    ) {}

    public function __invoke(DeletePageCommand $command): void
    {
        $page = $this->pages->findById($command->id)
            ?? throw new EntityNotFoundException(Page::class, $command->id);

        $page->delete();
        $this->pages->remove($page);
    }
}
