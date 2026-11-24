<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddDoctrineBridge\Type\VarcharType;

final class SlugType extends VarcharType
{
    public const string NAME = 'page_slug';

    protected string $name = self::NAME;
    protected int $length = Slug::MAX_LENGTH;
    protected string $voClass = Slug::class;
}
