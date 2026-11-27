<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Symfony;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Where the public sees a page, when the application says so
 * (`page.public_route`): the back office links to it and shows the address
 * before the slug field.
 */
final readonly class PublicPage
{
    private const string PLACEHOLDER = 'slug-placeholder';

    public function __construct(
        private UrlGeneratorInterface $urls,
        private ?string $route = null,
    ) {}

    public function url(string $slug): ?string
    {
        return $this->route === null ? null : $this->urls->generate($this->route, ['slug' => $slug]);
    }

    /**
     * The address up to the slug: `/`.
     */
    public function prefix(): ?string
    {
        $url = $this->url(self::PLACEHOLDER);
        if ($url === null || !str_ends_with($url, self::PLACEHOLDER)) {
            return null;
        }

        return substr($url, 0, -\strlen(self::PLACEHOLDER));
    }
}
