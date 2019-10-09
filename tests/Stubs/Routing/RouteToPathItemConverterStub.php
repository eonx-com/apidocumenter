<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface;

/**
 * @coversNothing
 */
final class RouteToPathItemConverterStub implements RouteToPathItemConverterInterface
{
    /**
     * @var \cebe\openapi\spec\PathItem[]
     */
    private $pathItems;

    /**
     * Constructor.
     *
     * @param \cebe\openapi\spec\PathItem[] $pathItems
     */
    public function __construct(array $pathItems)
    {
        $this->pathItems = $pathItems;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $routes): array
    {
        return $this->pathItems;
    }
}
