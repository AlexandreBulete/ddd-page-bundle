<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Service;

use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;

/**
 * Turns a title into a slug: transliterated, lowercased, punctuation dropped.
 * Not unique by itself.
 */
interface SlugGeneratorInterface
{
    public function slugify(string $text): Slug;
}
