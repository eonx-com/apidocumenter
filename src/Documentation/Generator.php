<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation;

use cebe\openapi\spec\OpenApi;
use JsonException;
use LoyaltyCorp\ApiDocumenter\Documentation\Exceptions\GenerationFailedException;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface;

final class Generator implements GeneratorInterface
{
    /**
     * The version of the OpenAPI specification we are targetting.
     *
     * @const string
     */
    private const TARGET_OPENAPI_VERSION = '3.0.2';

    /**
     * @var \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface
     */
    private $pathItemConverter;

    /**
     * @var \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface
     */
    private $routeExtractor;

    /**
     * @var \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface
     */
    private $schemaConverter;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface $pathItemConverter
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface $routeExtractor
     * @param \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface $schemaConverter
     */
    public function __construct(
        RouteToPathItemConverterInterface $pathItemConverter,
        RouteExtractorInterface $routeExtractor,
        RoutesToSchemasConverterInterface $schemaConverter
    ) {
        $this->pathItemConverter = $pathItemConverter;
        $this->routeExtractor = $routeExtractor;
        $this->schemaConverter = $schemaConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function generate(string $name, string $version): string
    {
        $routes = $this->routeExtractor->getRoutes();

        $schemas = $this->schemaConverter->convert($routes);
        $paths = $this->pathItemConverter->convert($routes);

        $root = new OpenApi([
            'openapi' => static::TARGET_OPENAPI_VERSION,
            'info' => [
                'title' => $name,
                'version' => $version,
            ],
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas,
            ],
        ]);

        try {
            return \json_encode(
                $root->getSerializableData(),
                \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT
            );
            // @codeCoverageIgnoreStart
            // Unable to force $root->getSerializableData() to return invalid objects
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (JsonException $exception) {
            throw new GenerationFailedException(
                'Documentation Generation failed.',
                0,
                $exception
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
