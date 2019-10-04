<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing\Interfaces;

interface RouteExtractorInterface
{
    /**
     * Returns all known routes to be considered for documenting.
     *
     * @return \LoyaltyCorp\ApiDocumenter\Routing\Route[]
     */
    public function getRoutes(): array;
}
