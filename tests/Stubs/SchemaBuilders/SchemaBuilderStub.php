<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\SchemaBuilders;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface;

class SchemaBuilderStub implements SchemaBuilderInterface
{
    /**
     * @var \cebe\openapi\spec\Schema[]
     */
    private $schemas;

    /**
     * Constructor
     *
     * @param \cebe\openapi\spec\Schema[] $schemas
     */
    public function __construct(?array $schemas = null)
    {
        $this->schemas = $schemas ?? [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function buildSchema(string $class): Schema
    {
        return $this->schemas[$class] ?? new Schema([]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $class): bool
    {
        return \array_key_exists($class, $this->schemas);
    }
}
