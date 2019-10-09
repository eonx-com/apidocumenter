<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation;

use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface;

final class RoutesToSchemasConverterStub implements RoutesToSchemasConverterInterface
{
    /**
     * @var \cebe\openapi\spec\Schema[]
     */
    private $schemas;

    /**
     * Constructor.
     *
     * @param \cebe\openapi\spec\Schema[] $schemas
     */
    public function __construct(array $schemas)
    {
        $this->schemas = $schemas;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $routes): array
    {
        return $this->schemas;
    }
}
