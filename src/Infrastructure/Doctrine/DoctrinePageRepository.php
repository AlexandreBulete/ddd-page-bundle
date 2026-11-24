<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\DomainEventPublisherInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type\SlugType;
use AlexandreBulete\DddDoctrineBridge\DoctrineRepository;
use AlexandreBulete\DddFoundation\Domain\ValueObject\IdentifierVO;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends DoctrineRepository<Page>
 */
final class DoctrinePageRepository extends DoctrineRepository implements PageRepositoryInterface
{
    private const string ALIAS = 'page';

    public function __construct(
        EntityManagerInterface $em,
        private readonly DomainEventPublisherInterface $eventPublisher,
    ) {
        parent::__construct($em, Page::class, self::ALIAS);
    }

    public function save(Page $page): void
    {
        $this->em->wrapInTransaction(function () use ($page): void {
            $this->em->persist($page);
            $this->em->flush();
            $this->eventPublisher->publishAll($page->releaseEvents());
        });
    }

    public function remove(Page $page): void
    {
        $this->em->wrapInTransaction(function () use ($page): void {
            $this->em->remove($page);
            $this->em->flush();
            $this->eventPublisher->publishAll($page->releaseEvents());
        });
    }

    public function findById(IdentifierVO $id): ?Page
    {
        return $this->em->find(Page::class, $id->value());
    }

    public function findBySlug(Slug $slug): ?Page
    {
        $page = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Page::class, 'p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug, SlugType::NAME)
            ->getQuery()
            ->getOneOrNullResult();

        return $page instanceof Page ? $page : null;
    }

    public function published(): array
    {
        /** @var list<Page> $pages */
        $pages = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Page::class, 'p')
            ->where('p.published = :published')
            ->setParameter('published', true)
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $pages;
    }
}
