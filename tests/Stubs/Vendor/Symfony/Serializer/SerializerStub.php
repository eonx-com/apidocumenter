<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Symfony\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @coversNothing
 */
final class SerializerStub implements SerializerInterface
{
    /**
     * @var mixed[]
     */
    private $calls;

    /**
     * @var mixed
     */
    private $object;

    /**
     * Constructor.
     *
     * @param mixed $object
     */
    public function __construct($object = null)
    {
        $this->object = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, ?array $context = null)
    {
        $this->calls[] = \compact('data', 'type', 'format', 'context');

        return $this->object;
    }

    /**
     * @return mixed[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = []): string
    {
        return 'serialized';
    }
}
