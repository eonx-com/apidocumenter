<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation\Interfaces;

interface RoutesToSchemasConverterInterface
{
    /**
     * Turns a list of routes into an array of Schemas, finding the most
     * appropriate builder to use for each class.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route[] $routes
     *
     * @return \cebe\openapi\spec\Schema[]
     */
    public function convert(array $routes): array;
}
