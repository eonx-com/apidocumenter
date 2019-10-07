<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator\Interfaces;

interface RouteConverterInterface
{
    /**
     * Converts an array of Route objects into the OpenApi PathItem objects.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route[] $routes
     *
     * @return \cebe\openapi\spec\PathItem[]
     */
    public function convert(array $routes): array;
}
