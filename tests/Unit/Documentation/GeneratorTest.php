<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Documentation;

use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\Documentation\Generator;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation\RoutesToSchemasConverterStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing\RouteEnhancerStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing\RouteExtractorStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing\RouteToPathItemConverterStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Documentation\Generator
 */
final class GeneratorTest extends TestCase
{
    /**
     * Tests generate.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testGenerate(): void
    {
        $route = new Route('', '', '', '');

        $pathItemConverter = new RouteToPathItemConverterStub([
            '/path/{param}' => new PathItem([
                'parameters' => [
                    new Parameter([
                        'in' => 'path',
                        'name' => 'param',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                ],
            ]),
        ]);
        $routeEnhancer = new RouteEnhancerStub();
        $routeExtractor = new RouteExtractorStub([
            $route
        ]);
        $schemaConverter = new RoutesToSchemasConverterStub([
            'schemaEntry' => new Schema([
                'type' => 'string',
            ]),
        ]);

        $generator = new Generator(
            $pathItemConverter,
            $routeEnhancer,
            $routeExtractor,
            $schemaConverter
        );

        $expected = <<<JSON
{
    "openapi": "3.0.2",
    "info": {
        "title": "Application Name",
        "version": "1.2.3"
    },
    "paths": {
        "\/path\/{param}": {
            "parameters": [
                {
                    "name": "param",
                    "in": "path",
                    "required": true,
                    "schema": {
                        "type": "string"
                    }
                }
            ]
        }
    },
    "components": {
        "schemas": {
            "schemaEntry": {
                "type": "string"
            }
        }
    }
}
JSON;

        $output = $generator->generate('Application Name', '1.2.3');

        self::assertSame($expected, $output);
        self::assertSame([$route], $routeEnhancer->getEnhanced());
    }
}
