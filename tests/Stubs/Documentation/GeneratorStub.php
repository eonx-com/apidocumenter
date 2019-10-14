<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation;

use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;

final class GeneratorStub implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $name, string $version, ?RouteExamples $examples = null): string
    {
        return 'generated output';
    }
}
