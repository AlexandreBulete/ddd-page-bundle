<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Text;

use AlexandreBulete\DddPageBundle\Domain\Service\SlugGeneratorInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Symfony's AsciiSlugger, cut to the length a Slug allows on a word boundary.
 */
final readonly class AsciiSlugGenerator implements SlugGeneratorInterface
{
    private SluggerInterface $slugger;

    public function __construct(?SluggerInterface $slugger = null)
    {
        $this->slugger = $slugger ?? new AsciiSlugger();
    }

    public function slugify(string $text): Slug
    {
        $slug = $this->slugger->slug($text)->lower()->toString();
        if (strlen($slug) > Slug::MAX_LENGTH) {
            $slug = substr($slug, 0, Slug::MAX_LENGTH);
            $cut = strrpos($slug, '-');
            $slug = $cut !== false && $cut > 0 ? substr($slug, 0, $cut) : $slug;
        }
        $slug = trim($slug, '-');

        if ($slug === '') {
            throw new \InvalidArgumentException(sprintf('No slug can be derived from "%s": give one explicitly.', $text));
        }

        return Slug::fromString($slug);
    }
}
