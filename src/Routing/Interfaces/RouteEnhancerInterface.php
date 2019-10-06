<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing\Interfaces;

use LoyaltyCorp\ApiDocumenter\Routing\Route;

interface RouteEnhancerInterface
{
    /**
     * The Route Enhancer will take a Route object and enhance it with additional
     * information like return types, request types, descriptions or other
     * information that is not present inside a typical router.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route $route
     *
     * @return void
     */
    public function enhanceRoute(Route $route): void;
}
