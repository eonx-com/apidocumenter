<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use DateTime as BaseDateTime;
use EoneoPay\Utils\DateTime;
use LoyaltyCorp\ApiDocumenter\Generator\OpenApiTypeResolver;
use Symfony\Component\PropertyInfo\Type;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\OpenApiTypeResolver
 */
final class OpenApiTypeResolverTest extends TestCase
{
    /**
     * Returns test data for the type resolver.
     *
     * @return mixed[]
     */
    public function getTestData(): iterable
    {
        yield 'a class' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT,
                true,
                Request::class
            ),
            'expected' => [null, null],
        ];

        yield 'a resource' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_RESOURCE
            ),
            'expected' => [null, null],
        ];

        yield 'an array' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_ARRAY
            ),
            'expected' => [null, null],
        ];

        yield 'null' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_NULL
            ),
            'expected' => [null, null],
        ];

        yield 'a callable' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_CALLABLE
            ),
            'expected' => [null, null],
        ];

        yield 'a datetime' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                BaseDateTime::class
            ),
            'expected' => ['string', 'date-time'],
        ];

        yield 'a utils datetime' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                DateTime::class
            ),
            'expected' => ['string', 'date-time'],
        ];

        yield 'an integer' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_INT
            ),
            'expected' => ['integer', 'int64'],
        ];

        yield 'a float' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_FLOAT
            ),
            'expected' => ['number', 'float'],
        ];

        yield 'a string' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_STRING
            ),
            'expected' => ['string', null],
        ];

        yield 'a bool' => [
            'type' => new Type(
                Type::BUILTIN_TYPE_BOOL
            ),
            'expected' => ['boolean', null],
        ];
    }

    /**
     * Tests that resolved property types match the OpenApi specification
     * for those types.
     *
     * @param \Symfony\Component\PropertyInfo\Type $type
     * @param mixed[] $expected
     *
     * @return void
     *
     * @dataProvider getTestData
     */
    public function testResolvedPropertyType(Type $type, array $expected): void
    {
        $resolver = new OpenApiTypeResolver();

        $actual = $resolver->resolvePropertyType($type);

        self::assertSame($expected, $actual);
    }
}
