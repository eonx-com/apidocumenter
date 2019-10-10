<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\ClassUtils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * An overridden version of Symfony's PropertyInfoExtractor - we filter out
 * specific properties on an object if they're prefixed with an underscore.
 *
 * Additionally, we combine the Doctrine collection types and any annotated
 * array collection type so that the Type returned matches what we expect.
 *
 * This means that we can annotate `Collection|Entity[]` and get back a single
 * type of Collection that is a collection, that contains Entity values.
 */
final class PropertyInfoExtractor implements PropertyInfoExtractorInterface
{
    /**
     * @var \Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface
     */
    private $propertyInfo;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface $propertyInfo
     */
    public function __construct(PropertyInfoExtractorInterface $propertyInfo)
    {
        $this->propertyInfo = $propertyInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($class, ?array $context = null): ?array
    {
        $properties = $this->propertyInfo->getProperties($class, $context ?? []) ?? [];

        $properties = \array_filter($properties, static function (string $name): bool {
            // Remove anything that starts with an underscore from being used in the Schema.
            return $name[0] !== '_' &&
                // Remove the statusCode property as a temporary measure to avoid
                // the getStatusCode on responses from being added to every
                // typed response.
                $name !== 'statusCode';
        });

        return \array_values($properties);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($class, $property, ?array $context = null): bool
    {
        return $this->propertyInfo->isReadable($class, $property, $context ?? []) ?: false;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($class, $property, ?array $context = null): bool
    {
        return $this->propertyInfo->isWritable($class, $property, $context ?? []) ?: false;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($class, $property, ?array $context = null): ?string
    {
        return $this->propertyInfo->getShortDescription($class, $property, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function getLongDescription($class, $property, ?array $context = null): ?string
    {
        return $this->propertyInfo->getLongDescription($class, $property, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, ?array $context = null): ?array
    {
        static $doctrineCollections = [
            ArrayCollection::class,
            Collection::class,
            LazyCriteriaCollection::class,
            PersistentCollection::class,
        ];

        $types = $this->propertyInfo->getTypes($class, $property, $context ?? []);

        if (\is_array($types) === false || \count($types) !== 2) {
            return $types;
        }

        $doctrineType = null;
        $collectionType = null;

        foreach ($types as $type) {
            if (\in_array($type->getClassName(), $doctrineCollections, true) === true) {
                $doctrineType = $type;

                continue;
            }

            if ($type->isCollection() === true) {
                $collectionType = $type;
            }
        }

        if ($doctrineType instanceof Type === true && $collectionType instanceof Type === true) {
            return [new Type(
                Type::BUILTIN_TYPE_OBJECT,
                $doctrineType->isNullable(),
                $doctrineType->getClassName(),
                true,
                $collectionType->getCollectionKeyType(),
                $collectionType->getCollectionValueType()
            )];
        }

        return $types;
    }
}
