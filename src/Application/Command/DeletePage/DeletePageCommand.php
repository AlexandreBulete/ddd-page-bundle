<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Command\DeletePage;

use AlexandreBulete\DddFoundation\Application\Command\CommandInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;

/**
 * Deletes a page for good.
 *
 * @implements CommandInterface<void>
 */
final readonly class DeletePageCommand implements CommandInterface
{
    public function __construct(
        public PageId $id,
    ) {}
}
