<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator\Interfaces;

use Symfony\Component\PropertyInfo\Type;

interface PropertyRetrieverInterface
{
    /**
     * Returns properties for the supplied class.
     *
     * @param string $class
     *
     * @return string[]
     */
    public function getProperties(string $class): array;

    /**
     * Returns the types for a property.
     *
     * @param string $class
     * @param string $property
     *
     * @return Type[]
     */
    public function getTypes(string $class, string $property): ?array;
}