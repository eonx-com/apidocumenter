<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Fixtures;

/**
 * @coversNothing
 */
final class TestController
{
    /**
     * Controller method.
     *
     * @param \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Request|null $request
     *
     * @return \Tests\LoyaltyCorp\ApiDocumenter\Fixtures\Response
     */
    public function method(?Request $request = null): Response
    {
        return new Response();
    }
}
