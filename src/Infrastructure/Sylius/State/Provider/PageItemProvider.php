<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\State\Provider;

use AlexandreBulete\DddPageBundle\Application\Query\FindPage\FindPageQuery;
use AlexandreBulete\DddPageBundle\Domain\Model\Page;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\PageId;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddFoundation\Application\Query\QueryBusInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Option\RequestOption;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProviderInterface;

final readonly class PageItemProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {}

    public function provide(Operation $operation, Context $context): ?PageResource
    {
        $id = $context->get(RequestOption::class)?->request()->attributes->getString('id');
        if ($id === null || $id === '') {
            return null;
        }

        /** @var Page $page */
        $page = $this->queryBus->ask(new FindPageQuery(PageId::fromString($id)));

        return PageResource::fromModel($page);
    }
}
