<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Tests\Integration;

use AlexandreBulete\DddPageBundle\Application\Command\ChangePageSlug\ChangePageSlugCommand;
use AlexandreBulete\DddPageBundle\Application\Command\ChangePageSlug\ChangePageSlugHandler;
use AlexandreBulete\DddPageBundle\Application\Command\CreatePage\CreatePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\CreatePage\CreatePageHandler;
use AlexandreBulete\DddPageBundle\Application\Command\PlacePage\PlacePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\PlacePage\PlacePageHandler;
use AlexandreBulete\DddPageBundle\Application\Command\PublishPage\PublishPageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\PublishPage\PublishPageHandler;
use AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPage\FindPublishedPageHandler;
use AlexandreBulete\DddPageBundle\Application\Query\FindPublishedPage\FindPublishedPageQuery;
use AlexandreBulete\DddPageBundle\Domain\Exception\PageNotFoundException;
use AlexandreBulete\DddPageBundle\Domain\Exception\PlacementTakenException;
use AlexandreBulete\DddPageBundle\Domain\Exception\UnknownPlacementException;
use AlexandreBulete\DddPageBundle\Domain\Exception\SlugAlreadyUsedException;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Config\ConfiguredPlacementCatalog;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\DoctrinePageRepository;
use AlexandreBulete\DddPageBundle\Infrastructure\Text\AsciiSlugGenerator;
use AlexandreBulete\DddPageBundle\Tests\Integration\Fixture\NullEventPublisher;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class PublishedPagesTest extends TestCase
{
    private EntityManagerInterface $em;
    private DoctrinePageRepository $pages;
    private MockClock $clock;

    protected function setUp(): void
    {
        $this->em = PageDatabase::migrated();
        $this->pages = new DoctrinePageRepository($this->em, new NullEventPublisher());
        $this->clock = new MockClock('2026-11-24 10:00:00');
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->close();
    }

    #[Test]
    public function only_pages_online_are_public_by_title(): void
    {
        $this->online($this->create('Politique de confidentialité'));
        $this->online($this->create('Mentions légales'));
        $this->create('Brouillon');
        $this->em->clear();

        self::assertSame(['Mentions légales', 'Politique de confidentialité'], array_map(static fn (Page $p): string => $p->title, $this->pages->published()));

        $find = new FindPublishedPageHandler($this->pages);
        self::assertSame('Mentions légales', $find(new FindPublishedPageQuery(Slug::fromString('mentions-legales')))->title);
        $this->expectException(PageNotFoundException::class);
        $find(new FindPublishedPageQuery(Slug::fromString('brouillon')));
    }

    #[Test]
    public function two_pages_never_share_an_address(): void
    {
        $this->create('Mentions légales');
        $other = $this->create('Crédits');

        $this->expectException(SlugAlreadyUsedException::class);
        (new ChangePageSlugHandler($this->pages, $this->clock))(new ChangePageSlugCommand($other->id, Slug::fromString('mentions-legales')));
    }

    #[Test]
    public function a_placement_lists_its_pages_online_only(): void
    {
        $this->place($this->online($this->create('Mentions légales')), 'legal');
        $this->place($this->create('Brouillon légal'), 'legal');
        $this->online($this->create('Ailleurs'));
        $this->em->clear();

        self::assertSame(['Mentions légales'], array_map(static fn (Page $p): string => $p->title, $this->pages->published(Placement::fromString('legal'))));
        self::assertCount(2, $this->pages->published());
    }

    #[Test]
    public function an_exclusive_placement_holds_one_page(): void
    {
        $this->place($this->create('À propos'), 'about');
        $this->place($this->create('Mentions'), 'legal');
        $other = $this->create('Qui suis-je');

        try {
            $this->place($other, 'about');
            self::fail('Expected the placement to be taken.');
        } catch (PlacementTakenException $e) {
            self::assertSame('À propos', $e->holder);
        }

        $this->expectException(UnknownPlacementException::class);
        $this->place($other, 'sidebar');
    }

    private function place(Page $page, ?string $placement): Page
    {
        $catalog = new ConfiguredPlacementCatalog([
            'legal' => ['label' => 'Footer', 'exclusive' => false],
            'about' => ['label' => 'About', 'exclusive' => true],
        ]);

        return (new PlacePageHandler($this->pages, $catalog))(new PlacePageCommand($page->id, $placement === null ? null : Placement::fromString($placement)));
    }

    private function create(string $title): Page
    {
        return (new CreatePageHandler($this->pages, new AsciiSlugGenerator(), $this->clock))(new CreatePageCommand($title, null, 'Texte.'));
    }

    private function online(Page $page): Page
    {
        return (new PublishPageHandler($this->pages, $this->clock))(new PublishPageCommand($page->id));
    }
}
