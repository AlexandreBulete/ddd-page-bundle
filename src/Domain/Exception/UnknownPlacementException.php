<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Exception;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

final class UnknownPlacementException extends \DomainException
{
    public function __construct(
        public readonly Placement $placement,
    ) {
        parent::__construct(sprintf('The site offers no placement "%s".', $placement->value()));
    }
}
