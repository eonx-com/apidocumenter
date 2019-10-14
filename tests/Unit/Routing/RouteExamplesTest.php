<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\RouteExample;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples
 */
final class RouteExamplesTest extends TestCase
{
    /**
     * Tests example methods.
     *
     * @return void
     */
    public function testMethods(): void
    {
        $example = new RouteExample(
            'description',
            'post',
            '/path',
            'request',
            'response',
            200,
            'summary'
        );

        $example2 = new RouteExample(
            'description',
            'get',
            '/path',
            'request',
            'response',
            200,
            'summary'
        );

        $example3 = new RouteExample(
            'description',
            'put',
            '/other',
            'request',
            'response',
            200,
            'summary'
        );

        $examples = new RouteExamples([$example, $example2, $example3]);

        self::assertSame([$example], $examples->getExamples('post', '/path'));
        self::assertSame([$example2], $examples->getExamples('get', '/path'));
        self::assertSame([], $examples->getExamples('patch', '/other'));
    }
}
