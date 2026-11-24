<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Tests\Integration;

use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MigrationMatchesMappingTest extends TestCase
{
    #[Test]
    public function the_migrated_schema_is_the_mapped_schema(): void
    {
        $em = PageDatabase::migrated();

        self::assertSame([], (new SchemaTool($em))->getUpdateSchemaSql($em->getMetadataFactory()->getAllMetadata()));
        $em->getConnection()->close();
    }
}
