<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\RequestBody;
use cebe\openapi\spec\Response as CebeResponse;
use cebe\openapi\spec\Responses;
use LoyaltyCorp\ApiDocumenter\Generator\RouteConverter;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\RouteConverter
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
        $route->setDescription('Description');
        $route->setDeprecated(false);
        $route->setRequestType(Request::class);
        $route->setResponseType(Response::class);
        $route->setSummary('Summary');

        yield 'single path' => [
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
                        'description' => 'Description',
                        'requestBody' => new RequestBody([
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest', // phpcs:ignore
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
                                    ],
                                ],
                            ]),
                        ]),
                        'summary' => 'Summary',
                    ]),
                ]),
            ],
        ];

        $route2 = new Route(
            TestController::class,
            'methodWithMultipleParams',
            'POST',
            '/path/{param}',
            ['param']
        );
        $route2->setDescription('Description');
        $route2->setDeprecated(true);
        $route2->setRequestType(Request::class);
        $route2->setResponseType(Response::class);
        $route2->setSummary('Summary');

        yield 'double methods' => [
            'routes' => [$route, $route2],
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
     *
     * @return void
     *
     * @dataProvider getTestData
     */
    public function testConversion(array $routes, array $pathItems): void
    {
        $converter = new RouteConverter();

        $result = $converter->convert($routes);

        self::assertEquals($pathItems, $result);
    }
}
