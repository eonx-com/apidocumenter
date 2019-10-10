<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures;

/**
 * @coversNothing
 */
final class DoctrineObject
{
    /**
     * Summary.
     *
     * phpcs:disable
     *
     * @var \Doctrine\Common\Collections\Collection|\Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass[]
     *
     * phpcs:enable
     */
    public $collection;
}
