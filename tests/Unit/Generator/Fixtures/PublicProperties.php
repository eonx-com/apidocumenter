<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures;

/**
 * @coversNothing
 */
final class PublicProperties
{
    /**
     * A property that should not appear anywhere because it is prefixed with an underscore.
     *
     * @var null
     */
    public $_skipThis;

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\EmptyClass
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
    public $string; // phpcs:ignore

    public $typeless;

        /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\ValueObject[]
     */
    public $values = []; // phpcs:ignore
}
