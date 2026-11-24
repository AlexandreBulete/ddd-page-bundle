<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Grid;

use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

class PageGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return self::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setProvider(PageGridProvider::class)
            ->orderBy('title', 'asc')
            ->addField(StringField::create('title')->setLabel('page.page.title')->setSortable(true))
            ->addField(StringField::create('slug')->setLabel('page.slug.label'))
            ->addField(TwigField::create('published', '@SyliusBootstrapAdminUi/shared/grid/field/boolean.html.twig')->setLabel('page.page.published')->setSortable(true))
            ->addField(DateTimeField::create('updatedAt')->setLabel('page.page.updated_at')->setSortable(true))
            ->addActionGroup(MainActionGroup::create(CreateAction::create()))
            ->addActionGroup(ItemActionGroup::create(UpdateAction::create(), DeleteAction::create()))
        ;
    }

    public function getResourceClass(): string
    {
        return PageResource::class;
    }
}
