<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Routing;

use cebe\openapi\spec\Example;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\RequestBody;
use cebe\openapi\spec\Response as CebeResponse;
use cebe\openapi\spec\Responses;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExample;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;
use LoyaltyCorp\ApiDocumenter\Routing\RouteToPathItemConverter;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Routing\RouteToPathItemConverter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Required to test
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) Required to test
 */
final class RouteConverterTest extends TestCase
{
    /**
     * Returns test data for testConversion.
     *
     * @return mixed[]
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function getTestData(): iterable
    {
        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path/{param}',
            ['param']
        );

        yield 'no data path' => [
            'routes' => [$route],
            'pathItems' => [
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
                    'get' => new Operation([
                        'deprecated' => false,
                    ]),
                ]),
            ],
        ];

        $route2 = new Route(
            TestController::class,
            'method',
            'GET',
            '/path/{param}',
            ['param']
        );
        $route2->setDescription('Description');
        $route2->setDeprecated(false);
        $route2->setRequestType(Request::class);
        $route2->setResponseType(Response::class);
        $route2->setSummary('Summary');

        yield 'single path with multiple examples' => [
            'routes' => [$route2],
            'pathItems' => [
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
                    'get' => new Operation([
                        'deprecated' => false,
                        'description' => 'Description',
                        'requestBody' => new RequestBody([
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest', // phpcs:ignore
                                    ],
                                    'examples' => [
                                        new Example([
                                            'summary' => 'example summary 1',
                                            'description' => 'example description 1',
                                            'value' => [
                                                'request' => 'data',
                                            ],
                                        ]),
                                    ],
                                ],
                            ],
                        ]),
                        'responses' => new Responses([
                            200 => new CebeResponse([
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse', // phpcs:ignore
                                        ],
                                        'examples' => [
                                            new Example([
                                                'summary' => 'example summary 1',
                                                'description' => 'example description 1',
                                                'value' => [
                                                    'response' => 'data',
                                                ],
                                            ]),
                                        ],
                                    ],
                                ],
                            ]),
                            204 => new CebeResponse([
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse', // phpcs:ignore
                                        ],
                                        'examples' => [],
                                    ],
                                ],
                            ]),
                        ]),
                        'summary' => 'Summary',
                    ]),
                ]),
            ],
            'examples' => [
                new RouteExample(
                    'example description 1',
                    'GET',
                    '/path/{param}',
                    '{"request": "data"}',
                    '{"response": "data"}',
                    200,
                    'example summary 1'
                ),
                new RouteExample(
                    'example description 2',
                    'GET',
                    '/path/{param}',
                    null,
                    null,
                    204,
                    'example summary 2'
                ),
            ],
        ];

        yield 'single path' => [
            'routes' => [$route2],
            'pathItems' => [
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
                    'get' => new Operation([
                        'deprecated' => false,
                        'description' => 'Description',
                        'requestBody' => new RequestBody([
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest', // phpcs:ignore
                                    ],
                                    'examples' => [],
                                ],
                            ],
                        ]),
                        'responses' => new Responses([
                            200 => new CebeResponse([
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse', // phpcs:ignore
                                        ],
                                        'examples' => [],
                                    ],
                                ],
                            ]),
                        ]),
                        'summary' => 'Summary',
                    ]),
                ]),
            ],
        ];

        $route3 = new Route(
            TestController::class,
            'methodWithMultipleParams',
            'POST',
            '/path/{param}',
            ['param']
        );
        $route3->setDescription('Description');
        $route3->setDeprecated(true);
        $route3->setRequestType(Request::class);
        $route3->setResponseType(Response::class);
        $route3->setSummary('Summary');

        yield 'double methods' => [
            'routes' => [$route2, $route3],
            'pathItems' => [
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
                    'get' => new Operation([
                        'deprecated' => false,
                        'description' => 'Description',
                        'requestBody' => new RequestBody([
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest', // phpcs:ignore
                                    ],
                                    'examples' => [],
                                ],
                            ],
                        ]),
                        'responses' => new Responses([
                            200 => new CebeResponse([
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse', // phpcs:ignore
                                        ],
                                        'examples' => [],
                                    ],
                                ],
                            ]),
                        ]),
                        'summary' => 'Summary',
                    ]),
                    'post' => new Operation([
                        'deprecated' => true,
                        'description' => 'Description',
                        'requestBody' => new RequestBody([
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest', // phpcs:ignore
                                    ],
                                    'examples' => [],
                                ],
                            ],
                        ]),
                        'responses' => new Responses([
                            200 => new CebeResponse([
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesResponse', // phpcs:ignore
                                        ],
                                        'examples' => [],
                                    ],
                                ],
                            ]),
                        ]),
                        'summary' => 'Summary',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * Tests conversion of routes into PathItem objects.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route[] $routes
     * @param \cebe\openapi\spec\PathItem[] $pathItems
     * @param \LoyaltyCorp\ApiDocumenter\Routing\RouteExample[]|null $examples
     *
     * @return void
     *
     * @dataProvider getTestData
     */
    public function testConversion(array $routes, array $pathItems, ?array $examples = null): void
    {
        $converter = new RouteToPathItemConverter();

        $result = $converter->convert($routes, new RouteExamples($examples ?? []));

        self::assertEquals($pathItems, $result);
    }
}
