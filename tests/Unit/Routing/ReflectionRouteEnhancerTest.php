<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Exceptions\RouteEnhancementFailedException;
use LoyaltyCorp\ApiDocumenter\Routing\ReflectionRouteEnhancer;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use phpDocumentor\Reflection\DocBlockFactory;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\RequestInterface;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\ResponseInterface;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Routing\ReflectionRouteEnhancer
 *
 * @SuppressWarnings(PHPMD.StaticAccess) Required to create DocBlockFactory
 */
final class ReflectionRouteEnhancerTest extends TestCase
{
    /**
     * Tests that the enhancer adds details to the Route object.
     *
     * @return void
     */
    public function testAddedDetails(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [],
            [
                RequestInterface::class,
            ]
        );

        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path'
        );

        $expectedRoute = clone $route;
        $expectedRoute->setSummary('Method Summary.');
        $expectedRoute->setDescription('This is the controller method\'s description.');
        $expectedRoute->setDeprecated(true);
        $expectedRoute->setRequestType(Request::class);
        $expectedRoute->setResponseType(Response::class);

        $enhancer->enhanceRoute($route);

        self::assertEquals($expectedRoute, $route);
    }

    /**
     * Tests that the enhancer supports looking for a value in multiple parameters.
     *
     * @return void
     */
    public function testMultipleParameters(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [],
            [
                RequestInterface::class,
            ]
        );

        $route = new Route(
            TestController::class,
            'methodWithMultipleParameters',
            'GET',
            '/path'
        );

        $expectedRoute = clone $route;
        $expectedRoute->setSummary('Multiple Parameters.');
        $expectedRoute->setRequestType(Request::class);
        $expectedRoute->setResponseType(Response::class);

        $enhancer->enhanceRoute($route);

        self::assertEquals($expectedRoute, $route);
    }

    /**
     * Tests what happens when the method has no comment.
     *
     * @return void
     */
    public function testNoComment(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [],
            [
            ]
        );

        $route = new Route(
            TestController::class,
            'noComment',
            'GET',
            '/path'
        );

        $expectedRoute = clone $route;
        // There should be no additional details added to the route.

        $enhancer->enhanceRoute($route);

        self::assertEquals($expectedRoute, $route);
    }

    /**
     * Tests that the enhancer adds details to the Route object.
     *
     * @return void
     */
    public function testIgnoresTypes(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [
                ResponseInterface::class,
            ],
            []
        );

        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path'
        );

        $expectedRoute = clone $route;
        $expectedRoute->setSummary('Method Summary.');
        $expectedRoute->setDescription('This is the controller method\'s description.');
        $expectedRoute->setDeprecated(true);
        $expectedRoute->setRequestType(null);
        $expectedRoute->setResponseType(null);

        $enhancer->enhanceRoute($route);

        self::assertEquals($expectedRoute, $route);
    }

    /**
     * Tests that the enhancer will not continue if it does not have at a minumum
     * the Controller class and method.
     *
     * @return void
     */
    public function testInvalidController(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [],
            []
        );

        $route = new Route(
            'nonExistant',
            'nonExistant',
            'GET',
            '/path'
        );

        $this->expectException(RouteEnhancementFailedException::class);
        $this->expectExceptionMessage('An error occurred trying to reflect details for route. (/path)');

        $enhancer->enhanceRoute($route);
    }

    /**
     * Tests that the enhancer will not continue if it does not have at a minumum
     * the Controller class and method.
     *
     * @return void
     */
    public function testInvalidControllerMethod(): void
    {
        $enhancer = new ReflectionRouteEnhancer(
            DocBlockFactory::createInstance(),
            [],
            []
        );

        $route = new Route(
            TestController::class,
            'nonExistant',
            'GET',
            '/path'
        );

        $this->expectException(RouteEnhancementFailedException::class);
        $this->expectExceptionMessage('An error occurred trying to reflect details for route. (/path)');

        $enhancer->enhanceRoute($route);
    }
}
