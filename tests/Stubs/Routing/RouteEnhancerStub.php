<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Route;

class RouteEnhancerStub implements RouteEnhancerInterface
{
    /**
     * @var Route[]
     */
    private $enhanced = [];

    /**
     * {@inheritdoc}
     */
    public function enhanceRoute(Route $route): void
    {
        $this->enhanced[] = $route;
    }

    /**
     * Returns enhanced routes.
     *
     * @return Route[]
     */
    public function getEnhanced(): array
    {
        return $this->enhanced;
    }
}
