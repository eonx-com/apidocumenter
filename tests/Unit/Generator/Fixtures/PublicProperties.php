<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures;

/**
 * @coversNothing
 */
final class PublicProperties
{
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
    public $string;

    public $typeless; // phpcs:ignore

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\ValueObject[]
     */
    public $values = [];
}
