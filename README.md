# ddd-page-bundle

Standalone pages — legal notice, privacy policy, "how I work" — as a reusable
DDD Bounded Context for Symfony: written in Markdown in a Sylius back office,
online or not, read through public queries.

## Installation

```bash
composer require alexandrebulete/ddd-page-bundle
bin/console doctrine:migrations:migrate
```

The Flex recipe registers the bundle. The `page` table comes with the
bundle's own migration.

## Model

| Aggregate | Holds |
|---|---|
| `Page` | title, slug, Markdown body, online or not, placement, created and last updated |

- A page starts **offline**. An empty page cannot go online, and a page online
  cannot be emptied.
- The last update date moves with the title, the address or the text — what a
  legal page has to show.
- No author, no category, no scheduling: that is a blog's job.
- A **placement** says where the site links the page — its footer, its menu,
  a reserved spot — or nowhere (reachable by its address only). The
  application declares the placements; an **exclusive** one holds a single
  page. Placing a page does not move its last update date.

## Use cases

Commands: `CreatePage` (slug derived from the title when none is given),
`RetitlePage`, `ChangePageSlug`, `RewritePage`, `PlacePage`, `PublishPage`,
`UnpublishPage`, `DeletePage`.

Queries: `FindPages`, `FindPage` (back office), `FindPublishedPage` and
`FindPublishedPages` (`#[PublicAccess]`). A page offline is not found, like an
unknown one.

Permissions follow the skeleton's convention: `page.create_page`,
`page.find_pages`…

## Showing a page

The bundle declares no public route: the application picks its URLs.

```php
#[Route('/{slug}', name: 'page', requirements: ['slug' => Slug::PATTERN], priority: -100)]
public function __invoke(string $slug, QueryBusInterface $queryBus): Response
{
    try {
        $page = $queryBus->ask(new FindPublishedPageQuery(Slug::fromString($slug)));
    } catch (PageNotFoundException) {
        throw $this->createNotFoundException();
    }

    return $this->render('page.html.twig', ['page' => $page]);
}
```

```twig
<h1>{{ page.title }}</h1>
<p>Updated {{ page.updatedAt|date('Y-m-d') }}</p>
{{ page.body|page_markdown }}

{# a footer: the pages online of the `legal` placement #}
{% for page in published_pages('legal') %}
    <a href="{{ path('page', {slug: page.slug.value}) }}">{{ page.title }}</a>
{% endfor %}

{# a reserved spot: the page online of an exclusive placement, or null #}
{% set about = placed_page('about') %}
```

`page_markdown` escapes raw HTML and drops unsafe links: its output is printed
as is. `published_pages()` (every page online without an argument) asks once
per request and placement.

## Configuration

```yaml
page:
    admin:
        enabled: true   # Sylius screens; false for a headless setup
    placements:         # where the site links pages; none by default
        legal: Footer — legal
        menu: Main menu
        about: { label: About page, exclusive: true }
```

Labels are translated in the `messages` domain: a key works as well as a
text. Renaming or dropping a placement leaves the pages on it in the database
but linked from nowhere — the template no longer asks for them.

## Seams

| Port | Default | Rebind for |
|---|---|---|
| `DomainEventPublisherInterface` | `ImmediateEventPublisher` (in-process, in the transaction) | an outbox |
| `PageRepositoryInterface` | Doctrine | another storage |
| `SlugGeneratorInterface` | `AsciiSlugGenerator` (Symfony String) | another transliteration |
| `PlacementCatalogInterface` | `ConfiguredPlacementCatalog` (`page.placements`) | placements stored elsewhere |

## Development

```bash
composer qa    # phpstan (max, strict) + deptrac + phpunit
```

Integration tests run on SQLite in memory, or on the database given by
`DDD_TEST_DATABASE_URL` (CI: PostgreSQL, MySQL, SQLite).

## License

MIT
