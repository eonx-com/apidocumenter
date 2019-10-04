<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Route;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Routing\Route
 */
final class RouteTest extends TestCase
{
    /**
     * Tests route methods.
     *
     * @return void
     */
    public function testMethods(): void
    {
        $route = new Route(
            TestController::class,
            'method',
            'GET',
            '/path',
            ['parameter']
        );

        $route->setRequestType(Request::class);
        $route->setResponseType(Response::class);
        $route->setDeprecated(true);
        $route->setDescription('description');
        $route->setSummary('summary');

        self::assertSame(TestController::class, $route->getControllerClass());
        self::assertSame('method', $route->getControllerMethod());
        self::assertTrue($route->isDeprecated());
        self::assertSame('description', $route->getDescription());
        self::assertSame('summary', $route->getSummary());
        self::assertSame('GET', $route->getMethod());
        self::assertSame('/path', $route->getPath());
        self::assertSame(['parameter'], $route->getParameters());
        self::assertSame(Request::class, $route->getRequestType());
        self::assertSame(Response::class, $route->getResponseType());
    }
}
