<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures;

/**
 * @coversNothing
 */
final class PrivateProperties
{
    /**
     * @var \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass
     */
    private $empty;

    /**
     * @var string
     */
    private $string;

    /**
     * Returns empty.
     *
     * @return \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\EmptyClass
     */
    public function getEmpty(): EmptyClass
    {
        return $this->empty;
    }

    /**
     * While the serialiser will not serialise 'fakeout' as a property, we still
     * support discovering the type here.
     *
     * @return \Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\PublicProperties
     */
    public function getFakeout(): PublicProperties
    {
        return new PublicProperties();
    }

    /**
     * Returns string.
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }
}
