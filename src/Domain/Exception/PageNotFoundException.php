<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Exception;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;

/**
 * No page online answers to this slug: unknown, or offline. One exception for
 * both, so the public side cannot tell them apart.
 */
final class PageNotFoundException extends \DomainException
{
    public function __construct(
        public readonly Slug $slug,
    ) {
        parent::__construct(sprintf('No page online for the slug "%s".', $slug->value()));
    }
}
