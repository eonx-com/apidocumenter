<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

use cebe\openapi\spec\Schema;
use LoyaltyCorp\ApiDocumenter\Generator\Exceptions\UnsupportedClassException;
use LoyaltyCorp\ApiDocumenter\Generator\Interfaces\SchemaBuilderInterface;

/**
 * This builder is used to build custom schema responses for entities that
 * are queried by fields other than "id".
 *
 * For example, in the subscriptions project, a coupon is not queried by "id"
 * but queried by "code".
 */
final class CustomEntityRequestSchemaBuilder implements SchemaBuilderInterface
{
    /**
     * Stores an array with key values being the class and the value being
     * the property that is used for the id property.
     *
     * @var string[]
     */
    private $classMap;

    /**
     * Constructor.
     *
     * @param string[] $classMap
     */
    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function buildSchema(string $class): Schema
    {
        if (\array_key_exists($class, $this->classMap) === false) {
            throw new UnsupportedClassException(
                \sprintf('The class "%s" is not supported by this schema builder.', $class)
            );
        }

        return new Schema([
            'properties' => [
                $this->classMap[$class] => new Schema(
                    [
                        'type' => 'string',
                    ]
                ),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $class): bool
    {
        return \array_key_exists($class, $this->classMap) === true;
    }
}
