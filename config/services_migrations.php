<?php

declare(strict_types=1);

use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Migrations\Version20261124100000;
use AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Migrations\Version20261126100000;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * Loaded only when DoctrineMigrationsBundle is enabled. Listed one by one,
 * never scanned: a stray class must not turn into a schema change.
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->tag('doctrine_migrations.migration');

    foreach ([Version20261124100000::class, Version20261126100000::class] as $migration) {
        $services->set($migration);
    }
};
