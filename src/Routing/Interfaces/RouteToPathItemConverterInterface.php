<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing\Interfaces;

use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;

interface RouteToPathItemConverterInterface
{
    /**
     * Converts an array of Route objects into the OpenApi PathItem objects.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route[] $routes
     * @param \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples $examples
     *
     * @return \cebe\openapi\spec\PathItem[]
     */
    public function convert(array $routes, RouteExamples $examples): array;
}
