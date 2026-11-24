<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the pages.
 *
 * DBAL's built-in types only, never the bundle's own: a migration is a
 * snapshot. The integration tests check that the result is the mapped schema
 * on PostgreSQL, MySQL and SQLite.
 */
final class Version20261124100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Page: standalone pages.';
    }

    public function up(Schema $schema): void
    {
        $page = $schema->createTable('page');
        $page->addColumn('id', Types::GUID);
        $page->addColumn('title', Types::STRING, ['length' => 200]);
        $page->addColumn('slug', Types::STRING, ['length' => 190]);
        $page->addColumn('body', Types::TEXT);
        $page->addColumn('published', Types::BOOLEAN);
        $page->addColumn('created_at', Types::DATETIME_IMMUTABLE);
        $page->addColumn('updated_at', Types::DATETIME_IMMUTABLE);
        $page->addPrimaryKeyConstraint(PrimaryKeyConstraint::editor()->setUnquotedColumnNames('id')->create());
        $page->addUniqueIndex(['slug']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('page');
    }
}
