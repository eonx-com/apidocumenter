<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation\Interfaces;

interface GeneratorInterface
{
    /**
     * Generates OpenAPI documentation based on the application.
     *
     * @param string $name
     * @param string $version
     *
     * @return string
     */
    public function generate(string $name, string $version): string;
}
