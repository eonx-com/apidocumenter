<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Generator\buildReference()
 */
final class BuildReferenceTest extends TestCase
{
    /**
     * Returns data for testBuildReference.
     *
     * @return string[]
     */
    public function getData(): iterable
    {
        yield 'simple reference' => ['ref', '#/components/schemas/ref'];
        yield 'class reference' => [
            Request::class,
            '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest',
        ];
    }

    /**
     * Tests that build reference correctly builds the expected reference.
     *
     * @param string $reference
     * @param string $expected
     *
     * @return void
     *
     * @dataProvider getData
     */
    public function testBuildReference(string $reference, string $expected): void
    {
        $result = \LoyaltyCorp\ApiDocumenter\Generator\buildReference($reference);

        self::assertSame($expected, $result);
    }
}
