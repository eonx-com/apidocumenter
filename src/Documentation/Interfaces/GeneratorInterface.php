<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation\Interfaces;

use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;

interface GeneratorInterface
{
    /**
     * Generates OpenAPI documentation based on the application.
     *
     * @param string $name
     * @param string $version
     * @param \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples|null $examples
     *
     * @return string
     */
    public function generate(string $name, string $version, ?RouteExamples $examples = null): string;
}
