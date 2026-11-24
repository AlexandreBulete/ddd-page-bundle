<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\CreatePage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;

/**
 * Writes a new page, offline. Without a slug, one is derived from the title.
 *
 * @implements CommandInterface<Page>
 */
final readonly class CreatePageCommand implements CommandInterface
{
    public function __construct(
        public string $title,
        public ?Slug $slug,
        public string $body,
    ) {}
}
