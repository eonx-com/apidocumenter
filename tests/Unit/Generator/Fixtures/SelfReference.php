<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures;

/**
 * @coversNothing
 */
final class SelfReference
{
    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\EmptyClass
     */
    public $empty;

    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\SelfReference
     */
    public $selfReference;
}
