<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Admin\Menu;

use AlexandreBulete\DddSyliusBundle\Admin\Menu\MenuContributorInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * "Pages": the standalone pages of the site.
 */
#[AutoconfigureTag('app.menu_contributor', ['priority' => 18])]
final readonly class PageMenuContributor implements MenuContributorInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorization,
    ) {}

    public function contribute(ItemInterface $menu): void
    {
        if (!$this->authorization->isGranted('page.find_pages')) {
            return;
        }

        $menu->addChild('pages', ['route' => 'page_admin_page_index'])
            ->setLabel('page.menu.pages')
            ->setLabelAttribute('icon', 'tabler:file-text');
    }
}
