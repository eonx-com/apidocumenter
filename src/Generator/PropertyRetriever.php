<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

use LoyaltyCorp\ApiDocumenter\Generator\Interfaces\PropertyRetrieverInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

final class PropertyRetriever implements PropertyRetrieverInterface
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
    public function getProperties(string $class): array
    {
        $properties = $this->propertyInfo->getProperties($class) ?? [];

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
    public function getTypes(string $class, string $property): ?array
    {
        return $this->propertyInfo->getTypes($class, $property);
    }
}
