<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD) Class is hanky, and triggers phpmd errors in order to test functionality
 */
final class PublicProperties
{
    /**
     * A property that should not appear anywhere because it is prefixed with an underscore.
     *
     * Longer Description.
     *
     * @var null
     */
    public $_skipThis; // phpcs:ignore

    /**
     * MULTIBALL.
     *
     * @var int|string
     */
    public $dualType;

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass
     */
    public $empty;

    /**
     * We cant do much with a resource. This should be skipped.
     *
     * @var resource
     */
    public $resource;

    /**
     * @var string
     */
    public $string;

    public $typeless; // phpcs:ignore

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\ValueObject[]
     */
    public $values = [];
}
