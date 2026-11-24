<?php

declare(strict_types=1);

use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\DomainEventPublisherInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\SlugGeneratorInterface;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\DoctrinePageRepository;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\ImmediateEventPublisher;
use AlexandreBulete\DddPageBundle\Infrastructure\Text\AsciiSlugGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $src = dirname(__DIR__) . '/src';
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('AlexandreBulete\\DddPageBundle\\', $src . '/')
        ->exclude([
            // Aggregates and value objects are never services.
            $src . '/Domain',
            $src . '/DddPageBundle.php',
            // Doctrine instantiates types itself.
            $src . '/Infrastructure/Doctrine/Type',
            $src . '/Infrastructure/Doctrine/Mapping',
            // Conditional: services_migrations.php, services_admin.php.
            $src . '/Infrastructure/Doctrine/Migrations',
            $src . '/Infrastructure/Sylius',
            $src . '/Infrastructure/Symfony',
        ]);

    // ── Ports → adapters ────────────────────────────────────────────────────
    $services->alias(PageRepositoryInterface::class, DoctrinePageRepository::class);
    $services->alias(DomainEventPublisherInterface::class, ImmediateEventPublisher::class);
    $services->alias(SlugGeneratorInterface::class, AsciiSlugGenerator::class);
};
