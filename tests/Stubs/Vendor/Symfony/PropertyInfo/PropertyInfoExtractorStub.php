<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Symfony\PropertyInfo;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

final class PropertyInfoExtractorStub implements PropertyInfoExtractorInterface
{
    /**
     * @var \Symfony\Component\PropertyInfo\Type[]
     */
    private $types;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\PropertyInfo\Type[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongDescription($class, $property, array $context = []): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($class, array $context = []): ?array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($class, $property, array $context = []): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = []): ?array
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($class, $property, array $context = []): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($class, $property, array $context = []): bool
    {
        return true;
    }
}
