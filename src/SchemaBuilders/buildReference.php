<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders;

// Only define the buildReference function if it doesnt already exist.
// @codeCoverageIgnoreStart
if (\function_exists(__NAMESPACE__ . '\buildReference') === false) { // phpcs:ignore
// @codeCoverageIgnoreEnd
    /**
     * Builds a reference for use inside the OpenAPI specification file.
     *
     * @param string $class
     * @param bool|null $addPrefix
     *
     * @return string
     */
    function buildReference(string $class, ?bool $addPrefix = null): string
    {
        $prefix = (($addPrefix ?? true) !== false)
            ? '#/components/schemas/'
            : '';

        return \sprintf(
            '%s%s',
            $prefix,
            \str_replace('\\', '', $class)
        );
    }
}
