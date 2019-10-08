<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator\Interfaces;

use cebe\openapi\spec\Schema;
use Symfony\Component\PropertyInfo\Type;

interface PropertyTypeToSchemaConverterInterface
{
    /**
     * Converts a PropertyInfo Type to a Schema.
     *
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return \cebe\openapi\spec\Schema|null
     */
    public function convert(Type $type): ?Schema;
}
