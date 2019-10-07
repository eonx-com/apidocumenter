<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols Squiz.WhiteSpace.ControlStructureSpacing.SpacingAfterOpen
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator;

// Only define the buildReference function if it doesnt already exist.
// @codeCoverageIgnoreStart
// The whitespace in this file is relevant to work around an issue with PHP-CS-Fixer:
// https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/3868

if (\function_exists(__NAMESPACE__ . '\buildReference') === false) { // phpcs:ignore

    // @codeCoverageIgnoreEnd
    false;

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
