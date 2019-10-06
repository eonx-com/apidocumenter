<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Fixtures;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD) This class will become a monster of permutations to test api documentation.
 */
final class TestController
{
    /**
     * Method Summary.
     *
     * This is the controller method's description.
     *
     * @param \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request|null $request
     *
     * @return \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response
     *
     * @deprecated Used to test Deprecation metadata
     */
    public function method(?Request $request = null): Response
    {
        return new Response();
    }

    /**
     * Multiple Parameters.
     *
     * @param string $thing
     * @param \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request $request
     *
     * @return \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response
     */
    public function methodWithMultipleParameters(string $thing, Request $request): Response
    {
        return new Response();
    }

    public function noComment(): void // phpcs:ignore
    {
        // This method intentionally has no comment to test comment conditions
        // in the Route Enhancer.
    }
}
