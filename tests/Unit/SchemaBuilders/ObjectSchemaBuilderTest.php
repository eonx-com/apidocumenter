<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\ClassUtils\PropertyInfoExtractor as WrappedPropertyInfoExtractor;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\OpenApiTypeResolver;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\PropertyTypeToSchemaConverter;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\PublicProperties;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\ValueObject;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Required to test
 */
final class ObjectSchemaBuilderTest extends TestCase
{
    /**
     * Returns build test data.
     *
     * @return mixed[]
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function getBuildData(): iterable
    {
        yield 'empty object' => [
            'class' => EmptyClass::class,
            'schema' => new Schema([
                'properties' => [],
                'type' => 'object',
            ]),
        ];

        yield 'value object' => [
            'class' => ValueObject::class,
            'schema' => new Schema([
                'properties' => [
                    'string' => new Schema([
                        'type' => 'string',
                        'description' => 'The value.',
                    ]),
                ],
                'required' => ['string'],
                'type' => 'object',
            ]),
        ];

        yield 'public properties object' => [
            'class' => PublicProperties::class,
            'schema' => new Schema([
                'properties' => [
                    'dual_type' => new Schema([
                        'oneOf' => [
                            new Schema([
                                'description' => 'MULTIBALL.',
                                'type' => 'integer',
                                'format' => 'int64',
                            ]),
                            new Schema([
                                'description' => 'MULTIBALL.',
                                'type' => 'string',
                            ]),
                        ],
                        'description' => 'MULTIBALL.',
                    ]),
                    'string' => new Schema([
                        'type' => 'string',
                    ]),
                    'empty' => new Schema([
                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterUnitSchemaBuildersFixturesEmptyClass', // phpcs:ignore
                    ]),
                    'values' => new Schema([
                        'items' => new Schema([
                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterUnitSchemaBuildersFixturesValueObject', // phpcs:ignore
                        ]),
                        'type' => 'array',
                    ]),
                ],
                'required' => [
                    'dual_type',
                    'empty',
                    'resource',
                    'string',
                    'values',
                ],
                'type' => 'object',
            ]),
        ];
    }

    /**
     * Tests the builder.
     *
     * @param string $class
     * @param \cebe\openapi\spec\Schema $expected
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     *
     * @dataProvider getBuildData
     */
    public function testBuild(string $class, Schema $expected): void
    {
        $builder = $this->getBuilder();

        $actual = $builder->buildSchema($class);

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that supports returns true for any object that actually exists.
     *
     * @return void
     */
    public function testSupports(): void
    {
        $builder = $this->getBuilder();

        self::assertFalse($builder->supports('PurpleElephant'));
        self::assertTrue($builder->supports(PublicProperties::class));
    }

    /**
     * Returns the builder under test.
     *
     * @return \LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder
     */
    private function getBuilder(): \LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor(
            null,
            null,
            null,
            true,
            ReflectionExtractor::ALLOW_PRIVATE |
            ReflectionExtractor::ALLOW_PROTECTED |
            ReflectionExtractor::ALLOW_PUBLIC
        );
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [],
            []
        );

        return new ObjectSchemaBuilder(
            new CamelCaseToSnakeCaseNameConverter(),
            new WrappedPropertyInfoExtractor($propertyInfo),
            new PropertyTypeToSchemaConverter(new OpenApiTypeResolver())
        );
    }
}
