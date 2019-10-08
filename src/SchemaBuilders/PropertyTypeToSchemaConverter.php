<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\OpenApiTypeResolverInterface;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\PropertyTypeToSchemaConverterInterface;
use Symfony\Component\PropertyInfo\Type;

final class PropertyTypeToSchemaConverter implements PropertyTypeToSchemaConverterInterface
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\OpenApiTypeResolverInterface
     */
    private $typeResolver;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\OpenApiTypeResolverInterface $typeResolver
     */
    public function __construct(OpenApiTypeResolverInterface $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function convert(Type $type): ?Schema
    {
        // If the type is a collection and has a value type, resolve
        // the collection type as an array.
        if ($type->isCollection() && $type->getCollectionValueType() instanceof Type === true) {
            return $this->handleCollectionType($type->getCollectionValueType());
        }

        // Try to resolve a primitive type
        [$primitiveType, $format] = $this->typeResolver->resolvePropertyType($type);

        // If we got a primitive type, return its schema.
        if (\is_string($primitiveType) === true &&
            ($format === null || \is_string($format) === true)
        ) {
            return $this->handlePrimitiveType($primitiveType, $format);
        }

        return $this->handleObjectType($type);
    }

    /**
     * Converts the collection value type and returns a Schema that indicates
     * it should be an array of types.
     *
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return \cebe\openapi\spec\Schema|null
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function handleCollectionType(Type $type): ?Schema
    {
        $collectionSchema = $this->convert($type);

        // If we didnt get a collection value schema we cant infer the schema for
        // this property at all.
        if ($collectionSchema instanceof Schema === false) {
            return null;
        }

        return new Schema([
            'items' => $collectionSchema,
            'type' => 'array',
        ]);
    }

    /**
     * Handles a type where no primitive type was resolved.
     *
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return \cebe\openapi\spec\Schema|null
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function handleObjectType(Type $type): ?Schema
    {
        $className = $type->getClassName();

        // If there is no class name or the class or interface doesnt exist
        // we cannot resolve a schema.
        if ($className === null ||
            (
                \class_exists($className) === false &&
                \interface_exists($className) === false
            )
        ) {
            return null;
        }

        $ref = \LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference($className);

        return new Schema([
            '$ref' => $ref,
        ]);
    }

    /**
     * Handles a primitive type.
     *
     * @param string $primitiveType
     * @param string|null $format
     *
     * @return \cebe\openapi\spec\Schema
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function handlePrimitiveType(string $primitiveType, ?string $format): Schema
    {
        $schema = new Schema([
            'type' => $primitiveType,
        ]);

        if ($format !== null) {
            $schema->format = $format;
        }

        return $schema;
    }
}
