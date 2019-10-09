<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Stubs\ClassFinder;

use LoyaltyCorp\ApiDocumenter\ClassUtils\Interfaces\ClassFinderInterface;

final class ClassFinderStub implements ClassFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(array $classes): array
    {
        return $classes;
    }
}
