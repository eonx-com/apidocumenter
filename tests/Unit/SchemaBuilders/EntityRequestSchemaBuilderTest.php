<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders;

use cebe\openapi\spec\Schema;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\EntityRequestSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Exceptions\UnsupportedClassException;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Externals\ORM\ManagerRegistryStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\PublicProperties;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\SchemaBuilders\EntityRequestSchemaBuilder
 */
final class EntityRequestSchemaBuilderTest extends TestCase
{
    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testBuildUnsupported(): void
    {
        $managerRegistry = new ManagerRegistryStub();
        $builder = new EntityRequestSchemaBuilder(
            $managerRegistry,
            null,
            [PublicProperties::class]
        );

        $this->expectException(UnsupportedClassException::class);
        $this->expectExceptionMessage(\sprintf(
            'The class "%s" is not supported by this schema builder.',
            PublicProperties::class
        ));

        $builder->buildSchema(PublicProperties::class);
    }

    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testBuild(): void
    {
        $factory = $this->createMock(ClassMetadataFactory::class);
        $factory->expects(self::once())
            ->method('isTransient')
            ->with(PublicProperties::class)
            ->willReturn(false);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($factory);

        $managerRegistry = new ManagerRegistryStub([
            PublicProperties::class => $manager,
        ]);

        $builder = new EntityRequestSchemaBuilder($managerRegistry);

        $expected = new Schema([
            'properties' => [
                'id' => new Schema([
                    'type' => 'string',
                ]),
            ],
        ]);

        $actual = $builder->buildSchema(PublicProperties::class);

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     */
    public function testSupportsSkips(): void
    {
        $managerRegistry = new ManagerRegistryStub();
        $builder = new EntityRequestSchemaBuilder(
            $managerRegistry,
            null,
            [PublicProperties::class]
        );

        $actual = $builder->supports(PublicProperties::class);

        self::assertFalse($actual);
    }

    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     */
    public function testSupportsFalseWhenNoEMReturned(): void
    {
        $managerRegistry = new ManagerRegistryStub();
        $builder = new EntityRequestSchemaBuilder($managerRegistry);

        $actual = $builder->supports(PublicProperties::class);

        self::assertFalse($actual);
    }

    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     */
    public function testSupportsFalseWhenTransient(): void
    {
        $factory = $this->createMock(ClassMetadataFactory::class);
        $factory->expects(self::once())
            ->method('isTransient')
            ->with(PublicProperties::class)
            ->willReturn(true);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($factory);

        $managerRegistry = new ManagerRegistryStub([
            PublicProperties::class => $manager,
        ]);
        $builder = new EntityRequestSchemaBuilder($managerRegistry);

        $actual = $builder->supports(PublicProperties::class);

        self::assertFalse($actual);
    }

    /**
     * Tests that supports listens to its internal $skipEntities array.
     *
     * @return void
     */
    public function testSupports(): void
    {
        $factory = $this->createMock(ClassMetadataFactory::class);
        $factory->expects(self::once())
            ->method('isTransient')
            ->with(PublicProperties::class)
            ->willReturn(false);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($factory);

        $managerRegistry = new ManagerRegistryStub([
            PublicProperties::class => $manager,
        ]);
        $builder = new EntityRequestSchemaBuilder($managerRegistry);

        $actual = $builder->supports(PublicProperties::class);

        self::assertTrue($actual);
    }
}
