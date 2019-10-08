<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\Generator\CustomEntityRequestSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\Generator\Exceptions\UnsupportedClassException;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\CustomEntityRequestSchemaBuilder
 */
final class CustomEntityRequestSchemaBuilderTest extends TestCase
{
    /**
     * Tests that the builder only supports what it is configured to support.
     *
     * @return void
     */
    public function testSupports(): void
    {
        $builder = new CustomEntityRequestSchemaBuilder([
            'PurpleElephants' => 'code'
        ]);

        self::assertFalse($builder->supports('GreenElephants'));
        self::assertTrue($builder->supports('PurpleElephants'));
    }

    /**
     * Tests that the builder only supports what it is configured to support.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testOutputSchema(): void
    {
        $builder = new CustomEntityRequestSchemaBuilder([
            'PurpleElephants' => 'code'
        ]);

        $expected = new Schema([
            'properties' => [
                'code' => new Schema([
                    'type' => 'string'
                ])
            ]
        ]);

        $actual = $builder->buildSchema('PurpleElephants');

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the builder only supports what it is configured to support.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testOutputSchemaUnsupported(): void
    {
        $builder = new CustomEntityRequestSchemaBuilder([
            'PurpleElephants' => 'code'
        ]);

        $this->expectException(UnsupportedClassException::class);
        $this->expectExceptionMessage('The class "GreenElephants" is not supported by this schema builder.');

        $builder->buildSchema('GreenElephants');
    }
}
