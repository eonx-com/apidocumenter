<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Bridge\Lumen;

use FastRoute\RouteParser\Std;
use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;
use LoyaltyCorp\ApiDocumenter\Bridge\Lumen\LumenRouteExtractor;
use LoyaltyCorp\ApiDocumenter\Routing\Route;
use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\TestController;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Bridge\Lumen\LumenRouteExtractor
 */
final class LumenRouteExtractorTest extends TestCase
{
    /**
     * Tests that the route extractor gets routes of the router.
     *
     * @return void
     */
    public function testGetRoutes(): void
    {
        // Yes, this is a mock - but we do not need the Router to use any methods
        // on this instance.
        $app = $this->createMock(Application::class);

        $router = new Router($app);

        $action = 'Tests\\LoyaltyCorp\\ApiDocumenter\\Fixtures\\TestController@method';

        $router->get('/first', $action);
        $router->get('/first/{thing}', $action);
        $router->group(['prefix' => '/prefixed'], static function () use ($action, $router): void {
            $router->post('/second/{thing}/{other}', $action);
        });

        $expected = [
            new Route(
                TestController::class,
                'method',
                'GET',
                '/first',
                []
            ),
            new Route(
                TestController::class,
                'method',
                'GET',
                '/first/{thing}',
                ['thing']
            ),
            new Route(
                TestController::class,
                'method',
                'POST',
                '/prefixed/second/{thing}/{other}',
                ['thing', 'other']
            ),
        ];

        $extractor = new LumenRouteExtractor($router, new Std());

        $result = $extractor->getRoutes();

        self::assertEquals($expected, $result);
    }
}
