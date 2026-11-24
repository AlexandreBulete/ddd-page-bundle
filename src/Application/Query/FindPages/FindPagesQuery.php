<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Application\Query\FindPages;

use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddFoundation\Application\Query\QueryInterface;

/**
 * Every page, online or not, for the back office.
 *
 * @implements QueryInterface<PageRepositoryInterface>
 */
final readonly class FindPagesQuery implements QueryInterface
{
    /**
     * @param array<string, mixed>  $criteria
     * @param array<string, string> $withSorting
     */
    public function __construct(
        public ?int $page = null,
        public ?int $itemsPerPage = null,
        public array $criteria = [],
        public array $withSorting = [],
    ) {}
}
