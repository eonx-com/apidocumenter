<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures;

/**
 * @coversNothing
 */
final class SelfReference
{
    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass
     */
    public $empty;

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\SelfReference
     */
    public $selfReference;
}
