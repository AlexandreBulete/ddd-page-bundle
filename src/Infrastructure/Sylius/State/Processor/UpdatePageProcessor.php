<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor;

use AlexandreBulete\DddPageBundle\Application\Command\ChangePageSlug\ChangePageSlugCommand;
use AlexandreBulete\DddPageBundle\Application\Command\PlacePage\PlacePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\PublishPage\PublishPageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\RetitlePage\RetitlePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\RewritePage\RewritePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\UnpublishPage\UnpublishPageCommand;
use AlexandreBulete\DddPageBundle\Application\Query\FindPage\FindPageQuery;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Placement;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddFoundation\Application\Command\CommandBusInterface;
use AlexandreBulete\DddFoundation\Application\Query\QueryBusInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

/**
 * One form, several intents: each field that changed is its own use case.
 * Taking a page offline comes before emptying it; filling it, before putting
 * it online.
 */
final readonly class UpdatePageProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {}

    public function process(mixed $data, Operation $operation, Context $context): PageResource
    {
        Assert::isInstanceOf($data, PageResource::class);
        Assert::isInstanceOf($data->id, Ulid::class);
        Assert::stringNotEmpty($data->title);

        $id = PageId::fromUlid($data->id);
        /** @var Page $page */
        $page = $this->queryBus->ask(new FindPageQuery($id));

        if (!$data->published && $page->published) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new UnpublishPageCommand($id));
        }
        if ($data->title !== $page->title) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new RetitlePageCommand($id, $data->title));
        }
        if ($data->slug !== null && $data->slug !== '' && $data->slug !== $page->slug->value()) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new ChangePageSlugCommand($id, Slug::fromString($data->slug)));
        }
        $body = $data->body ?? '';
        if ($body !== $page->body) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new RewritePageCommand($id, $body));
        }
        $placement = $data->placement !== null && $data->placement !== '' ? $data->placement : null;
        if ($placement !== $page->placement?->value()) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new PlacePageCommand($id, $placement === null ? null : Placement::fromString($placement)));
        }
        if ($data->published && !$page->published) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new PublishPageCommand($id));
        }

        return PageResource::fromModel($page);
    }
}
