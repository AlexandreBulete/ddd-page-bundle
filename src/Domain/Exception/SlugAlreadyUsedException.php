<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Exception;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;

final class SlugAlreadyUsedException extends \DomainException
{
    public function __construct(
        public readonly Slug $slug,
    ) {
        parent::__construct(sprintf('Another page already uses the slug "%s".', $slug->value()));
    }
}
