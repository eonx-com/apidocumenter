<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation;

use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;

final class GeneratorStub implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $name, string $version): string
    {
        return 'generated output';
    }
}
