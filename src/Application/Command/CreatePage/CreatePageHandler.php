<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\CreatePage;

use AlexandreBulete\DddFoundation\Application\Command\AsCommandHandler;
use Psr\Clock\ClockInterface;
use AlexandreBulete\DddPageBundle\Domain\Exception\SlugAlreadyUsedException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\SlugGeneratorInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

#[AsCommandHandler]
final readonly class CreatePageHandler
{
    public function __construct(
        private PageRepositoryInterface $pages,
        private SlugGeneratorInterface $slugger,
        private ClockInterface $clock,
    ) {}

    public function __invoke(CreatePageCommand $command): Page
    {
        $slug = $command->slug ?? $this->slugger->slugify($command->title);
        if ($this->pages->findBySlug($slug) !== null) {
            throw new SlugAlreadyUsedException($slug);
        }

        $page = Page::create(PageId::generate(), $command->title, $slug, $command->body, $this->clock->now());
        $this->pages->save($page);

        return $page;
    }
}
