<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\PropertyTypeToSchemaConverterInterface;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Turns an object into a Schema by using all properties of the object
 * and introspecting types.
 */
final class ObjectSchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @var \Symfony\Component\Serializer\NameConverter\NameConverterInterface
     */
    private $nameConverter;

    /**
     * @var \Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    /**
     * @var \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\PropertyTypeToSchemaConverterInterface
     */
    private $propertyToSchema;

    /**
     * Constructor.
     *
     * phpcs:disable
     * Definitions are longer than 120 chars.
     *
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     * @param \Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface $propertyInfo
     * @param \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\PropertyTypeToSchemaConverterInterface $propertyToSchema
     *
     * phpcs:enable
     */
    public function __construct(
        NameConverterInterface $nameConverter,
        PropertyInfoExtractorInterface $propertyInfo,
        PropertyTypeToSchemaConverterInterface $propertyToSchema
    ) {
        $this->nameConverter = $nameConverter;
        $this->propertyInfo = $propertyInfo;
        $this->propertyToSchema = $propertyToSchema;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function buildSchema(string $class): Schema
    {
        $propertyInfo = $this->propertyInfo->getProperties($class) ?? [];

        $schema = new Schema([
            'properties' => [],
            'required' => [],
            'type' => 'object',
        ]);

        foreach ($propertyInfo as $property) {
            $this->resolveProperty($schema, $class, $property);
        }

        if (\count($schema->required) === 0) {
            unset($schema->required);
        }

        return $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $class): bool
    {
        // This builder supports all objects.
        return \class_exists($class);
    }

    /**
     * Helper method due to a design flaw of the Schema object.
     *
     * @param \cebe\openapi\spec\Schema $schema
     * @param string $propertyName
     * @param \cebe\openapi\spec\Schema $property
     *
     * @return void
     */
    private function addProperty(Schema $schema, string $propertyName, Schema $property): void
    {
        $name = $this->nameConverter->normalize($propertyName);

        // The property already exists in the properties array.
        if (\array_key_exists($name, $schema->properties) === true) {
            // @codeCoverageIgnoreStart
            // Because $properties is built from real objects it isnt possible to test.
            return;
            // @codeCoverageIgnoreEnd
        }

        // Add the property to the required array. This dance is necessary because
        // with the Schema object's implementation we must trigger the __set call.
        $properties = $schema->properties;
        $properties[$name] = $property;

        $schema->properties = $properties;
    }

    /**
     * Helper method due to a design flaw of the Schema object.
     *
     * @param \cebe\openapi\spec\Schema $schema
     * @param string $propertyName
     *
     * @return void
     */
    private function addRequired(Schema $schema, string $propertyName): void
    {
        $name = $this->nameConverter->normalize($propertyName);

        // The property already exists in the required array.
        if (\in_array($name, $schema->required, true) === true) {
            // @codeCoverageIgnoreStart
            // Because $properties is built from real objects it isnt possible to test.
            return;
            // @codeCoverageIgnoreEnd
        }

        // Add the property to the required array. This dance is necessary because
        // with the Schema object's implementation we must trigger the __set call.
        $required = $schema->required;
        $required[] = $name;

        $schema->required = $required;
    }

    /**
     * Resolves the types of a property and inserts it into the Schema.
     *
     * @param \cebe\openapi\spec\Schema $schema
     * @param string $class
     * @param string $property
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function resolveProperty(Schema $schema, string $class, string $property): void
    {
        $types = $this->propertyInfo->getTypes($class, $property);

        // If there are no types for the property we cannot add any information.
        if ($types === null) {
            return;
        }

        $resolvedSchemas = [];

        $description = $this->propertyInfo->getShortDescription($class, $property);

        // Resolve each of the types into a schema, if possible.
        foreach ($types as $type) {
            if ($type->isNullable() === false) {
                // The property is not nullable, it should be in the required array.
                $this->addRequired($schema, $property);
            }

            $resolvedSchema = $this->propertyToSchema->convert($type);

            // No schema was resolved, we cant output any detail.
            if ($resolvedSchema instanceof Schema === false) {
                continue;
            }

            // If we dont have a reference, add the description. Descriptions cannot exist
            // on a schema with a reference.
            if ($description !== null && $resolvedSchema->__isset('$ref') === false) {
                $resolvedSchema->description = $description;
            }

            $resolvedSchemas[] = $resolvedSchema;
        }

        // We didnt end up resolving any schemas for the types on this property.
        if (\count($resolvedSchemas) === 0) {
            return;
        }

        // Since theres only one schema, add it direcrtly.
        if (\count($resolvedSchemas) === 1) {
            $this->addProperty($schema, $property, \reset($resolvedSchemas));

            return;
        }

        // Add a oneOf Schema that requires the property resolve to one of the multiple types
        // we resolved.
        $this->addProperty($schema, $property, new Schema([
            'description' => $description,
            'oneOf' => $resolvedSchemas,
        ]));
    }
}
