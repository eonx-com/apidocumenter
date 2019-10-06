<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

/**
 * Builds a reference for use inside the OpenAPI specification file.
 *
 * @param string $class
 *
 * @return string
 */
function buildReference(string $class): string
{
    return \sprintf(
        '#/components/schemas/%s',
        \str_replace('\\', '', $class)
    );
}
