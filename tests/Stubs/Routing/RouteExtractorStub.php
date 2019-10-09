<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface;

/**
 * @coversNothing
 */
final class RouteExtractorStub implements RouteExtractorInterface
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\Routing\Route[]
     */
    private $routes;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
