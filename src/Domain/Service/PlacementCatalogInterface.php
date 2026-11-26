<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Service;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

/**
 * The placements a site offers. An exclusive one holds a single page (an
 * "about" page); the others, as many as needed (a footer).
 */
interface PlacementCatalogInterface
{
    public function has(Placement $placement): bool;

    public function isExclusive(Placement $placement): bool;
}
