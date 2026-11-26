<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource;

use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Grid\PageGrid;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor\CreatePageProcessor;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor\DeletePageProcessor;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor\UpdatePageProcessor;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Provider\PageItemProvider;
use AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Form\PageType;
use AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Validator\UniquePageSlug;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[AsResource(
    alias: 'page.page',
    section: 'admin',
    formType: PageType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/admin',
    driver: false,
    operations: [
        new Create(processor: CreatePageProcessor::class),
        new Update(provider: PageItemProvider::class, processor: UpdatePageProcessor::class),
        new Delete(provider: PageItemProvider::class, processor: DeletePageProcessor::class),
        new Index(grid: PageGrid::class),
    ],
)]
#[UniquePageSlug]
final class PageResource implements ResourceInterface
{
    public function __construct(
        public ?AbstractUid $id = null,
        #[Assert\NotBlank]
        #[Assert\Length(max: Page::TITLE_MAX_LENGTH)]
        public ?string $title = null,
        #[Assert\Regex('/^' . Slug::PATTERN . '$/', message: 'page.slug.format')]
        #[Assert\Length(max: Slug::MAX_LENGTH)]
        public ?string $slug = null,
        public ?string $body = null,
        public bool $published = false,
        public ?\DateTimeImmutable $updatedAt = null,
        public ?string $placement = null,
        /** The placement's label, for the list. */
        public ?string $placementLabel = null,
    ) {}

    public function getId(): ?AbstractUid
    {
        return $this->id;
    }

    /**
     * The domain refuses it too; here it is an error under the field, before
     * anything is written.
     */
    #[Assert\Callback]
    public function validateOnline(ExecutionContextInterface $context): void
    {
        if ($this->published && trim($this->body ?? '') === '') {
            $context->buildViolation('page.page.empty_online')->atPath('body')->addViolation();
        }
    }

    public static function fromModel(Page $page, ?string $placementLabel = null): self
    {
        return new self(
            id: $page->id->value(),
            title: $page->title,
            slug: $page->slug->value(),
            body: $page->body,
            published: $page->published,
            updatedAt: $page->updatedAt,
            placement: $page->placement?->value(),
            placementLabel: $placementLabel,
        );
    }
}
