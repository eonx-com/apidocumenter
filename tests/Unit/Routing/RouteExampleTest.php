<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\RouteExample;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Routing\RouteExample
 */
final class RouteExampleTest extends TestCase
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
            'method',
            '/path',
            'request',
            'response',
            200,
            'summary'
        );

        self::assertSame('description', $example->getDescription());
        self::assertSame('method', $example->getMethod());
        self::assertSame('/path', $example->getPath());
        self::assertSame('request', $example->getRequestData());
        self::assertSame('response', $example->getResponseData());
        self::assertSame(200, $example->getResponseStatusCode());
        self::assertSame('summary', $example->getSummary());
    }
}
