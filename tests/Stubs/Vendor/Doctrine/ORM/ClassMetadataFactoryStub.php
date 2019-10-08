<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Doctrine\ORM;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class ClassMetadataFactoryStub implements ClassMetadataFactory
{
    /**
     * @var bool[]
     */
    private $transient;

    /**
     * Constructor
     *
     * @param bool[]|null $transient
     */
    public function __construct(?array $transient = null)
    {
        $this->transient = $transient ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMetadata()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($className)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($className)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataFor($className, $class): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        return $this->transient[$className] ?? true;
    }
}
