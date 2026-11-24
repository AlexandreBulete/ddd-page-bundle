<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Processor;

use AlexandreBulete\DddPageBundle\Application\Command\DeletePage\DeletePageCommand;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddFoundation\Application\Command\CommandBusInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

final readonly class DeletePageProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {}

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, PageResource::class);
        Assert::isInstanceOf($data->id, Ulid::class);
        $this->commandBus->dispatch(new DeletePageCommand(PageId::fromUlid($data->id)));

        return null;
    }
}
