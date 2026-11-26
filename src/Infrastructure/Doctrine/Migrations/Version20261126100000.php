<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Where a site links a page. Expand-only: nullable, existing pages are
 * linked from nowhere until placed.
 */
final class Version20261126100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Page: placements.';
    }

    public function up(Schema $schema): void
    {
        $page = $schema->getTable('page');
        $page->addColumn('placement', Types::STRING, ['length' => 50, 'notnull' => false]);
        $page->addIndex(['placement'], 'idx_page_placement');
    }

    public function down(Schema $schema): void
    {
        $page = $schema->getTable('page');
        $page->dropIndex('idx_page_placement');
        $page->dropColumn('placement');
    }
}
