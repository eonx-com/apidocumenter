<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders;

use cebe\openapi\spec\Schema;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Exceptions\UnsupportedClassException;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface;

/**
 * Converts doctrine entities into Schema objects. This builder is intended for use
 * when serialising entities for requests only - it does not serialise properties
 * of an entity, only an id property that should be looked up against the externalId
 * property (which is done by the DoctrineDenormalizer implementation in
 * loyaltycorp/requesthandlers).
 *
 * If you need different functionality, entity serialisation should be done in an
 * application defined builder.
 */
final class EntityRequestSchemaBuilder implements SchemaBuilderInterface
{
    /**
     * The default Id property that we expect to see the ID in.
     *
     * @var string
     */
    private $idProperty;

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var string[]
     */
    private $skipEntities;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     * @param string|null $idProperty
     * @param string[]|null $skipEntities
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        ?string $idProperty = null,
        ?array $skipEntities = null
    ) {
        $this->idProperty = $idProperty ?? 'id';
        $this->managerRegistry = $managerRegistry;
        $this->skipEntities = $skipEntities ?? [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function buildSchema(string $class): Schema
    {
        if ($this->supports($class) === false) {
            throw new UnsupportedClassException(
                \sprintf('The class "%s" is not supported by this schema builder.', $class)
            );
        }

        return new Schema([
            'properties' => [
                $this->idProperty => new Schema(
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
        // We're explictly skipping an entity so a custom builder can be used.
        if (\in_array($class, $this->skipEntities, true) === true) {
            return false;
        }

        // Try to get an entity manager for the class
        $objectManager = $this->managerRegistry->getManagerForClass($class);

        // No entity manager was returned.
        if ($objectManager instanceof ObjectManager === false) {
            return false;
        }

        // The metadata factory does not have any data for $class - which means it isnt
        // an entity.
        return $objectManager->getMetadataFactory()
            ->isTransient($class) === false;
    }
}
