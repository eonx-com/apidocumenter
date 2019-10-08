<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces;

use Symfony\Component\PropertyInfo\Type;

interface OpenApiTypeResolverInterface
{
    /**
     * Returns the OpenAPI type to be used for the property type.
     *
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return mixed[]
     */
    public function resolvePropertyType(Type $type): array;
}
