<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\RequestBody;
use cebe\openapi\spec\Responses;
use LoyaltyCorp\ApiDocumenter\Routing\Exceptions\RouteConversionFailedException;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface;

final class RouteToPathItemConverter implements RouteToPathItemConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(array $routes): array
    {
        $paths = [];

        foreach ($routes as $route) {
            if (\array_key_exists($route->getPath(), $paths) === false) {
                $paths[$route->getPath()] = $this->createPathItem($route);
            }

            try {
                $operation = $this->processRoute($route);
                // @codeCoverageIgnoreStart
                // Unable to make this exception occur to be tested.
            } catch (TypeErrorException $exception) {
                throw new RouteConversionFailedException(
                    'An exception occurred while converting routes.',
                    0,
                    $exception
                );
                // @codeCoverageIgnoreEnd
            }

            $method = \mb_strtolower($route->getMethod());
            /** @noinspection PhpVariableVariableInspection Required for OpenAPI spec */
            $paths[$route->getPath()]->{$method} = $operation;
        }

        return $paths;
    }

    /**
     * Creates a new PathItem.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route $route
     *
     * @return \cebe\openapi\spec\PathItem
     */
    private function createPathItem(Route $route): PathItem
    {
        $parameters = \array_map(
            static function ($name): Parameter {
                return new Parameter(
                    [
                        'in' => 'path',
                        'name' => $name,
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]
                );
            },
            $route->getParameters()
        );

        return new PathItem([
            'parameters' => $parameters,
        ]);
    }

    /**
     * Converts a Route into an Operation.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\Route $route
     *
     * @return \cebe\openapi\spec\Operation
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function processRoute(Route $route): Operation
    {
        $operation = new Operation([
            // 'callbacks' => [], // TODO: PYMT-1356 Webhook Callbacks
            'deprecated' => $route->isDeprecated(),
            // 'security' => null, // TODO: PYMT-1355 Security information in documentation
            'responses' => [],
        ]);

        if (\is_string($route->getSummary()) === true) {
            $operation->summary = $route->getSummary();
        }

        if (\is_string($route->getDescription()) === true) {
            $operation->description = $route->getDescription();
        }

        if (\is_string($route->getRequestType()) === true) {
            $ref = \LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference($route->getRequestType());

            $operation->requestBody = new RequestBody([
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => $ref,
                        ],
                    ],
                ],
            ]);
        }

        if (\is_string($route->getResponseType()) === true) {
            $ref = \LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference($route->getResponseType());

            $operation->responses = new Responses([
                '200' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => $ref,
                            ],
                        ],
                    ],
                ],
            ]);
        }

        return $operation;
    }
}
