<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use LoyaltyCorp\ApiDocumenter\Generator\PropertyRetriever;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator\Fixtures\PublicProperties;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\PropertyRetriever
 */
final class PropertyRetrieverTest extends TestCase
{
    /**
     * Tests that the property retriever retrieves properties.
     *
     * @return void
     */
    public function testGetProperties(): void
    {
        $retriever = $this->getRetriever();

        $expected = [
            'empty',
            'resource',
            'string',
            'typeless',
            'values'
        ];

        $actual = $retriever->getProperties(PublicProperties::class);

        static::assertSame($expected, $actual);
    }

    /**
     * Tests that the property retriever retrieves properties.
     *
     * @return void
     */
    public function testGetTypes(): void
    {
        $retriever = $this->getRetriever();

        $expected = [
            new Type(
                Type::BUILTIN_TYPE_STRING,
                false,
                null,
                false,
                null,
                null
            )
        ];

        $actual = $retriever->getTypes(PublicProperties::class, 'string');

        static::assertEquals($expected, $actual);
    }

    /**
     * Returns the retriever under test.
     *
     * @return PropertyRetriever
     */
    private function getRetriever(): PropertyRetriever
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor(
            null,
            null,
            null,
            true,
            ReflectionExtractor::ALLOW_PRIVATE |
            ReflectionExtractor::ALLOW_PROTECTED |
            ReflectionExtractor::ALLOW_PUBLIC
        );
        $propertyInfo = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [],
            []
        );

        return new PropertyRetriever($propertyInfo);
    }
}
