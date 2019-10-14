<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\spec\Example;
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
    public function convert(array $routes, RouteExamples $examples): array
    {
        $paths = [];

        foreach ($routes as $route) {
            if (\array_key_exists($route->getPath(), $paths) === false) {
                $paths[$route->getPath()] = $this->createPathItem($route);
            }

            try {
                $operation = $this->processRoute($route, $examples);
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
     * Adds a request to the operation, if a request type is known.
     *
     * @param \cebe\openapi\spec\Operation $operation
     * @param \cebe\openapi\spec\Example[] $requestExamples
     * @param string|null $requestType
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function addRequest(Operation $operation, array $requestExamples, ?string $requestType): void
    {
        if ($requestType === null) {
            return;
        }

        $ref = \LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference($requestType);

        $operation->requestBody = new RequestBody(
            [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => $ref,
                        ],
                        'examples' => $requestExamples,
                    ],
                ],
            ]
        );
    }

    /**
     * Adds any responses and their examples to the operation.
     *
     * @param \cebe\openapi\spec\Operation $operation
     * @param \cebe\openapi\spec\Example[][] $responseExamples
     * @param string|null $responseType
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function addResponses(Operation $operation, array $responseExamples, ?string $responseType): void
    {
        // We have no data to add to the operation.
        if ($responseType === null && \count($responseExamples) === 0) {
            return;
        }

        $schema = [];
        if ($responseType !== null) {
            $schema = [
                'schema' => [
                    '$ref' => \LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference($responseType),
                ],
                'examples' => [],
            ];
        }

        $responses = [];

        // We've got no examples so we cannot infer any status codes
        // this endpoint will return. We do have a schema reference,
        // so at a minimum we can populate the schema of a "200" response.
        if (\count($responseExamples) === 0) {
            $responses['200'] = [
                'content' => [
                    'application/json' => $schema,
                ],
            ];
        }

        foreach ($responseExamples as $statusCode => $examples) {
            $responses[(string)$statusCode] = [
                'content' => [
                    'application/json' => \array_merge(
                        $schema,
                        [
                            'examples' => $examples,
                        ]
                    ),
                ],
            ];
        }

        $operation->responses = new Responses($responses);
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
     * @param \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples $examples
     *
     * @return \cebe\openapi\spec\Operation
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    private function processRoute(Route $route, RouteExamples $examples): Operation
    {
        $operation = new Operation([
            // 'callbacks' => [], // TODO: PYMT-1356 Webhook Callbacks
            'deprecated' => $route->isDeprecated(),
            // 'security' => null, // TODO: PYMT-1355 Security information in documentation
        ]);

        if (\is_string($route->getSummary()) === true) {
            $operation->summary = $route->getSummary();
        }

        if (\is_string($route->getDescription()) === true) {
            $operation->description = $route->getDescription();
        }

        $requestExamples = [];
        $responseExamples = [];

        $pathExamples = $examples->getExamples($route->getMethod(), $route->getPath());

        foreach ($pathExamples as $example) {
            // Only add a request example if the Example has request data.
            if (\is_string($example->getRequestData()) === true) {
                $requestExamples[] = new Example([
                    'description' => $example->getDescription(),
                    'summary' => $example->getSummary(),
                    'value' => \json_decode(
                        $example->getRequestData(),
                        true,
                        512,
                        \JSON_THROW_ON_ERROR
                    ),
                ]);
            }

            // Ensure our responseExamples contains any status codes of any examples,
            // so we can indicate which response codes will be returned.
            $statusCode = $example->getResponseStatusCode();
            if (\array_key_exists($statusCode, $responseExamples) === false) {
                $responseExamples[$statusCode] = [];
            }

            if (\is_string($example->getResponseData()) === true) {
                $responseExamples[$statusCode][] = new Example([
                    'description' => $example->getDescription(),
                    'summary' => $example->getSummary(),
                    'value' => \json_decode(
                        $example->getResponseData(),
                        true,
                        512,
                        \JSON_THROW_ON_ERROR
                    ),
                ]);
            }
        }

        $this->addRequest(
            $operation,
            $requestExamples,
            $route->getRequestType()
        );

        $this->addResponses(
            $operation,
            $responseExamples,
            $route->getResponseType()
        );

        return $operation;
    }
}
