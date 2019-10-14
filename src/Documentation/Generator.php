<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation;

use cebe\openapi\spec\OpenApi;
use JsonException;
use LoyaltyCorp\ApiDocumenter\Documentation\Exceptions\GenerationFailedException;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;

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
     * @var \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface
     */
    private $routeEnhancer;

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
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface $routeEnhancer
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface $routeExtractor
     * @param \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface $schemaConverter
     */
    public function __construct(
        RouteToPathItemConverterInterface $pathItemConverter,
        RouteEnhancerInterface $routeEnhancer,
        RouteExtractorInterface $routeExtractor,
        RoutesToSchemasConverterInterface $schemaConverter
    ) {
        $this->pathItemConverter = $pathItemConverter;
        $this->routeEnhancer = $routeEnhancer;
        $this->routeExtractor = $routeExtractor;
        $this->schemaConverter = $schemaConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function generate(string $name, string $version, ?RouteExamples $examples = null): string
    {
        $routes = $this->routeExtractor->getRoutes();
        foreach ($routes as $route) {
            $this->routeEnhancer->enhanceRoute($route);
        }

        $schemas = $this->schemaConverter->convert($routes);
        $paths = $this->pathItemConverter->convert($routes, $examples ?? new RouteExamples([]));

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
                'An exception occurred converting the schema to JSON.',
                0,
                $exception
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
