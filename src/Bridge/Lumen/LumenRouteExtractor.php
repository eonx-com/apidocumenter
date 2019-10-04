<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Lumen;

use FastRoute\RouteParser\Std as RouteParser;
use Laravel\Lumen\Routing\Router;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Route;

final class LumenRouteExtractor implements RouteExtractorInterface
{
    /**
     * @var \FastRoute\RouteParser\Std
     */
    private $routeParser;

    /**
     * @var \Laravel\Lumen\Routing\Router
     */
    private $router;

    /**
     * Constructor.
     *
     * @param \Laravel\Lumen\Routing\Router $router
     * @param \FastRoute\RouteParser\Std $routeParser
     */
    public function __construct(Router $router, RouteParser $routeParser)
    {
        $this->router = $router;
        $this->routeParser = $routeParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->router->getRoutes() as $route) {
            [$class, $method] = \explode('@', $route['action']['uses']);
            $parameters = $this->extractParameters($route['uri']);

            $routes[] = new Route(
                $class,
                $method,
                $route['method'],
                $route['uri'],
                $parameters
            );
        }

        return $routes;
    }

    /**
     * Parses a route uri to extract parameters.
     *
     * @param string $uri
     *
     * @return string[]
     */
    private function extractParameters(string $uri): array
    {
        $parsed = $this->routeParser->parse($uri);
        $first = \reset($parsed) ?: [];

        $parameters = \array_filter($first, static function ($element): bool {
            return \is_array($element) === true;
        });

        $parameterNames = \array_map(static function (array $parameter): string {
            return $parameter[0];
        }, $parameters);

        return \array_values($parameterNames);
    }
}
