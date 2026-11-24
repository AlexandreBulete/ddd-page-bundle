<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddDoctrineBridge\Type\GuidType;

final class PageIdType extends GuidType
{
    public const string NAME = 'page_id';

    protected string $name = self::NAME;

    protected string $voClass = PageId::class;
}
