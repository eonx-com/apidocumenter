<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Generator;

use Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

class BuildReferenceTest extends TestCase
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
            '#/components/schemas/TestsLoyaltyCorpApiDocumenterFixturesRequest'
        ];
    }

    /**
     * Tests that build reference correctly builds the expected reference.
     *
     * @param string $in
     * @param string $expected
     *
     * @return void
     *
     * @dataProvider getData
     */
    public function testBuildReference(string $in, string $expected): void
    {
        $result = \LoyaltyCorp\ApiDocumenter\Generator\buildReference($in);

        static::assertSame($expected, $result);
    }
}
