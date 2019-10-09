<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Documentation;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\Documentation\Exceptions\NoSchemaBuilderFoundException;
use LoyaltyCorp\ApiDocumenter\Documentation\RoutesToSchemasConverter;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\ClassFinder\ClassFinderStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\SchemaBuilders\SchemaBuilderStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Documentation\RoutesToSchemasConverter
 */
final class RoutesToSchemasConverterTest extends TestCase
{
    /**
     * Tests the conversion process when no schema builder is found.
     *
     * @return void
     */
    public function testConvertNoBuilderFound(): void
    {
        $converter = new RoutesToSchemasConverter([], new ClassFinderStub());

        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path'
        );
        $route->setRequestType(Request::class);

        $this->expectException(NoSchemaBuilderFoundException::class);
        $this->expectExceptionMessage('No schema builder was found for "Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request"');

        $converter->convert([$route]);
    }

    /**
     * Tests building process.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testConvert(): void
    {
        $requestSchema = new Schema([
            'description' => 'Request',
        ]);
        $responseSchema = new Schema([
            'description' => 'Response',
        ]);

        $converter = new RoutesToSchemasConverter([
            new SchemaBuilderStub(),
            new SchemaBuilderStub([
                Request::class => $requestSchema,
                Response::class => $responseSchema,
            ]),
        ], new ClassFinderStub());

        $expected = [
            '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest' => $requestSchema,
            '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse' => $responseSchema,
        ];

        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path'
        );
        $route->setRequestType(Request::class);
        $route->setResponseType(Response::class);

        $result = $converter->convert([$route]);

        self::assertEquals($expected, $result);
    }
}
