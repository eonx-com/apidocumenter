<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces;

use cebe\openapi\spec\Schema;

interface SchemaBuilderInterface
{
    /**
     * Builds a Schema object from a PHP class.
     *
     * @param string $class
     *
     * @return \cebe\openapi\spec\Schema
     */
    public function buildSchema(string $class): Schema;

    /**
     * Indicates that this schema builder supports building a schema
     * for the specific class.
     *
     * @param string $lcass
     *
     * @return bool
     */
    public function supports(string $lcass): bool;
}
