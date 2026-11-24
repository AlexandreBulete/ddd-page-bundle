<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle;

use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type\PageIdType;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Type\SlugType;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Standalone pages (legal notice, privacy policy…), written in Markdown in a
 * Sylius back office and read through public queries.
 *
 * @phpstan-type PageConfig array{admin: array{enabled: bool}}
 */
final class DddPageBundle extends AbstractBundle
{
    protected string $extensionAlias = 'page';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Sylius back-office screens. Turn off for a headless deployment.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        /** @var PageConfig $config */
        $container->import($this->getPath() . '/config/services.php');
        if ($config['admin']['enabled']) {
            $container->import($this->getPath() . '/config/services_admin.php');
        }
        if (self::migrationsEnabled($builder)) {
            $container->import($this->getPath() . '/config/services_migrations.php');
        }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('doctrine', [
            'dbal' => ['types' => [PageIdType::NAME => PageIdType::class, SlugType::NAME => SlugType::class]],
            'orm' => [
                'mappings' => [
                    'Page' => [
                        'type' => 'xml',
                        'is_bundle' => false,
                        'dir' => $this->getPath() . '/src/Infrastructure/Doctrine/Mapping',
                        'prefix' => 'AlexandreBulete\\DddPageBundle\\Domain\\Model',
                        'alias' => 'Page',
                    ],
                ],
            ],
        ]);
        if (self::migrationsEnabled($builder)) {
            $builder->prependExtensionConfig('doctrine_migrations', ['enable_service_migrations' => true]);
        }
        $builder->prependExtensionConfig('framework', [
            'translator' => ['paths' => [$this->getPath() . '/translations']],
        ]);

        if (self::adminEnabled($builder)) {
            // DddSyliusBundle only globs the application's own resources.
            $builder->prependExtensionConfig('sylius_resource', [
                'mapping' => ['paths' => [$this->getPath() . '/src/Infrastructure/Sylius/Resource']],
            ]);
        }
    }

    /**
     * Before the config tree is processed: the default (on) needs its own
     * fallback here.
     */
    private static function adminEnabled(ContainerBuilder $builder): bool
    {
        /** @var list<array<string, mixed>> $configs */
        $configs = $builder->getExtensionConfig('page');
        $enabled = true;
        foreach ($configs as $config) {
            $admin = $config['admin'] ?? null;
            if (is_array($admin) && is_bool($admin['enabled'] ?? null)) {
                $enabled = $admin['enabled'];
            }
        }

        return $enabled;
    }

    private static function migrationsEnabled(ContainerBuilder $builder): bool
    {
        /** @var array<string, class-string> $bundles */
        $bundles = $builder->getParameter('kernel.bundles');

        return in_array(DoctrineMigrationsBundle::class, $bundles, true);
    }
}
