<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Externals\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD) This stub is from a vendor.
 */
final class ManagerRegistryStub implements ManagerRegistry
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager[]
     */
    private $managers;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager[] $managers
     */
    public function __construct(?array $managers = null)
    {
        $this->managers = $managers ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnectionName()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null)
    {
        return $this->managers[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class)
    {
        return $this->managers[$class] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerNames()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers(): array
    {
        return $this->managers;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null)
    {
    }
}
