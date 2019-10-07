<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\Generator\OpenApiTypeResolver;
use LoyaltyCorp\ApiDocumenter\Generator\PropertyTypeToSchemaConverter;
use Symfony\Component\PropertyInfo\Type;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\PublicProperties;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\PropertyTypeToSchemaConverter
 */
final class PropertyTypeToSchemaConverterTest extends TestCase
{
    /**
     * Returns test data.
     *
     * @return mixed
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function getConversionData(): iterable
    {
        yield 'object type' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                PublicProperties::class
            ),
            'schema' => new Schema([
                '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterUnitGeneratorFixturesPublicProperties' // phpcs:ignore
            ])
        ];

        yield 'object type with no class' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT
            ),
            'schema' => null
        ];

        yield 'non existant object type' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                'doesntexist'
            ),
            'schema' => null
        ];

        yield 'scalar string' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_STRING
            ),
            'schema' => new Schema([
                'type' => 'string'
            ])
        ];

        yield 'scalar integer' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_INT
            ),
            'schema' => new Schema([
                'type' => 'integer',
                'format' => 'int64'
            ])
        ];

        yield 'collection integer' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                null,
                new Type(
                    Type::BUILTIN_TYPE_INT
                )
            ),
            'schema' => new Schema([
                'items' => new Schema([
                    'type' => 'integer',
                    'format' => 'int64'
                ]),
                'type' => 'array'
            ])
        ];

        yield 'collection object' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                null,
                new Type(
                    Type::BUILTIN_TYPE_OBJECT,
                    false,
                    PublicProperties::class
                )
            ),
            'schema' => new Schema([
                'items' => new Schema([
                    '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterUnitGeneratorFixturesPublicProperties' // phpcs:ignore
                ]),
                'type' => 'array'
            ])
        ];

        yield 'collection unknown object' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                null,
                new Type(
                    Type::BUILTIN_TYPE_OBJECT,
                    false,
                    'doesntexist'
                )
            ),
            'schema' => null
        ];
    }

    /**
     * Tests the converter.
     *
     * @param Type $type
     * @param Schema $expected
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     *
     * @dataProvider getConversionData
     */
    public function testConversion(Type $type, ?Schema $expected): void
    {
        $typeResolver = new OpenApiTypeResolver();
        $converter = new PropertyTypeToSchemaConverter($typeResolver);

        $actual = $converter->convert($type);

        self::assertEquals($expected, $actual);
    }
}
