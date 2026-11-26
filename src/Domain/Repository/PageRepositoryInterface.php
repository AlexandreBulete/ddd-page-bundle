<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Repository;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddFoundation\Domain\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<Page>
 */
interface PageRepositoryInterface extends RepositoryInterface
{
    public function save(Page $page): void;

    public function remove(Page $page): void;

    public function findBySlug(Slug $slug): ?Page;

    /**
     * Every page online, by title; only those of a placement when given.
     *
     * @return list<Page>
     */
    public function published(?Placement $placement = null): array;

    /**
     * Every page of a placement, online or not.
     *
     * @return list<Page>
     */
    public function placedAt(Placement $placement): array;
}
