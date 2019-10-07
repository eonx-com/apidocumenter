<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

// Only define the buildReference function if it doesnt already exist.
// @codeCoverageIgnoreStart
if (\function_exists(__NAMESPACE__ . '\buildReference') === false) { // phpcs:ignore
// @codeCoverageIgnoreEnd
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
}
