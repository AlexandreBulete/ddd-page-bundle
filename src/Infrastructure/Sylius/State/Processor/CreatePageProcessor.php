<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor;

use AlexandreBulete\DddPageBundle\Application\Command\CreatePage\CreatePageCommand;
use AlexandreBulete\DddPageBundle\Application\Command\PublishPage\PublishPageCommand;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddFoundation\Application\Command\CommandBusInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class CreatePageProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {}

    public function process(mixed $data, Operation $operation, Context $context): PageResource
    {
        Assert::isInstanceOf($data, PageResource::class);
        Assert::stringNotEmpty($data->title);

        /** @var Page $page */
        $page = $this->commandBus->dispatch(new CreatePageCommand(
            title: $data->title,
            slug: $data->slug !== null && $data->slug !== '' ? Slug::fromString($data->slug) : null,
            body: $data->body ?? '',
        ));
        if ($data->published) {
            /** @var Page $page */
            $page = $this->commandBus->dispatch(new PublishPageCommand($page->id));
        }

        return PageResource::fromModel($page);
    }
}
