<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Exception;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

/**
 * An exclusive placement holds one page: the other one is moved off it
 * first, on purpose.
 */
final class PlacementTakenException extends \DomainException
{
    public function __construct(
        public readonly Placement $placement,
        public readonly string $holder,
    ) {
        parent::__construct(sprintf('The page "%s" already holds the placement "%s".', $holder, $placement->value()));
    }
}
