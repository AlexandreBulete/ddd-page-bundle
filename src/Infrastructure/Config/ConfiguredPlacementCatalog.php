<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Config;

use AlexandreBulete\DddPageBundle\Domain\Service\PlacementCatalogInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

/**
 * The placements declared under `page.placements`.
 */
final readonly class ConfiguredPlacementCatalog implements PlacementCatalogInterface
{
    /**
     * @param array<string, array{label: string, exclusive: bool}> $placements
     */
    public function __construct(
        private array $placements = [],
    ) {}

    public function has(Placement $placement): bool
    {
        return isset($this->placements[$placement->value()]);
    }

    public function isExclusive(Placement $placement): bool
    {
        return $this->placements[$placement->value()]['exclusive'] ?? false;
    }

    /**
     * For a choice field: label => key, in declaration order.
     *
     * @return array<string, string>
     */
    public function choices(): array
    {
        $choices = [];
        foreach ($this->placements as $key => $placement) {
            $choices[$placement['label']] = $key;
        }

        return $choices;
    }

    public function label(string $key): ?string
    {
        return $this->placements[$key]['label'] ?? null;
    }
}
