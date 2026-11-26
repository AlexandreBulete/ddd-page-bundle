<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type;

use AlexandreBulete\DddDoctrineBridge\Type\VarcharType;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;

final class PlacementType extends VarcharType
{
    public const string NAME = 'page_placement';

    protected string $name = self::NAME;
    protected int $length = Placement::MAX_LENGTH;
    protected string $voClass = Placement::class;
}
