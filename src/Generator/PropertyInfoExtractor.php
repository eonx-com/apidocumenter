<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

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
        return $this->propertyInfo->getTypes($class, $property, $context ?? []);
    }
}
