<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\ClassUtils;

use Doctrine\Common\Collections\Collection;
use LoyaltyCorp\ApiDocumenter\ClassUtils\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor as BasePropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Symfony\PropertyInfo\PropertyInfoExtractorStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;
use Tests\LoyaltyCorp\ApiDocumenter\Unit\SchemaBuilders\Fixtures\PublicProperties;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\ClassUtils\PropertyInfoExtractor
 */
final class PropertyInfoExtractorTest extends TestCase
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
            'dualType',
            'empty',
            'resource',
            'string',
            'typeless',
            'values',
        ];

        $actual = $retriever->getProperties(PublicProperties::class);

        self::assertSame($expected, $actual);
    }

    /**
     * Tests that the property retriever retrieves properties.
     *
     * @return void
     */
    public function testGetTypeCollectionCollapsing(): void
    {
        $propertyStub = new PropertyInfoExtractorStub([
            new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                Collection::class
            ),
            new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                new Type(Type::BUILTIN_TYPE_INT),
                new Type(Type::BUILTIN_TYPE_STRING)
            ),
        ]);

        $retriever = $this->getRetriever($propertyStub);

        $expected = [
            new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                Collection::class,
                true,
                new Type(Type::BUILTIN_TYPE_INT),
                new Type(Type::BUILTIN_TYPE_STRING)
            ),
        ];

        $actual = $retriever->getTypes(PublicProperties::class, 'string');

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the property retriever retrieves properties.
     *
     * @return void
     */
    public function testDualTypesNotCollection(): void
    {
        $propertyStub = new PropertyInfoExtractorStub([
            new Type(
                Type::BUILTIN_TYPE_STRING
            ),
            new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                new Type(Type::BUILTIN_TYPE_INT),
                new Type(Type::BUILTIN_TYPE_STRING)
            ),
        ]);

        $retriever = $this->getRetriever($propertyStub);

        $expected = [
            new Type(
                Type::BUILTIN_TYPE_STRING
            ),
            new Type(
                Type::BUILTIN_TYPE_ARRAY,
                false,
                null,
                true,
                new Type(Type::BUILTIN_TYPE_INT),
                new Type(Type::BUILTIN_TYPE_STRING)
            ),
        ];

        $actual = $retriever->getTypes(PublicProperties::class, 'string');

        self::assertEquals($expected, $actual);
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
            ),
        ];

        $actual = $retriever->getTypes(PublicProperties::class, 'string');

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests the passthrough methods.
     *
     * @return void
     */
    public function testMiscMethods(): void
    {
        $retriever = $this->getRetriever();

        self::assertFalse($retriever->isWritable(PublicProperties::class, 'string'));
        self::assertFalse($retriever->isReadable(PublicProperties::class, 'string'));
        self::assertSame(
            'A property that should not appear anywhere because it is prefixed with an underscore.',
            $retriever->getShortDescription(PublicProperties::class, '_skipThis')
        );
        self::assertSame('Longer Description.', $retriever->getLongDescription(PublicProperties::class, '_skipThis'));
    }

    /**
     * Returns the retriever under test.
     *
     * @param null|PropertyInfoExtractorInterface $propertyInfo
     *
     * @return \LoyaltyCorp\ApiDocumenter\ClassUtils\PropertyInfoExtractor
     */
    private function getRetriever(?PropertyInfoExtractorInterface $propertyInfo = null): PropertyInfoExtractor
    {
        if ($propertyInfo === null) {
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
            $propertyInfo = new BasePropertyInfoExtractor(
                [$reflectionExtractor],
                [$phpDocExtractor, $reflectionExtractor],
                [$phpDocExtractor],
                [],
                []
            );
        }

        return new PropertyInfoExtractor($propertyInfo);
    }
}
