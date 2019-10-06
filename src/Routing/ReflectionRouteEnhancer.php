<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

use LoyaltyCorp\ApiDocumenter\Routing\Exceptions\RouteEnhancementFailedException;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

final class ReflectionRouteEnhancer implements RouteEnhancerInterface
{
    /**
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    private $docFactory;

    /**
     * Holds an array of interfaces to use to exclude response types. If a response
     * type of a method implements any of the provided interfaces it will not be added
     * as the return type.
     *
     * @var string[]
     */
    private $excludeResponses;

    /**
     * Holds an array of interfaces to be used to try to find the request object for
     * the controller method.
     *
     * @var string[]
     */
    private $requestInterfaces;

    /**
     * Constructor.
     *
     * @param \phpDocumentor\Reflection\DocBlockFactory $docFactory
     * @param string[] $excludeResponses
     * @param string[] $requestInterfaces
     */
    public function __construct(
        DocBlockFactory $docFactory,
        array $excludeResponses,
        array $requestInterfaces
    ) {
        $this->docFactory = $docFactory;
        $this->excludeResponses = $excludeResponses;
        $this->requestInterfaces = $requestInterfaces;
    }

    /**
     * {@inheritdoc}
     */
    public function enhanceRoute(Route $route): void
    {
        try {
            $class = new ReflectionClass($route->getControllerClass());
            $method = $class->getMethod($route->getControllerMethod());
        } catch (ReflectionException $exception) {
            throw new RouteEnhancementFailedException(
                \sprintf(
                    'An error occurred trying to reflect details for route. (%s)',
                    $route->getPath()
                ),
                0,
                $exception
            );
        }

        // Set the request type if one can be found.
        $requestType = $this->findRequestType($method);
        $route->setRequestType($requestType);

        // Set the response type if there is one and it isnt blacklisted.
        $returnType = $this->findResponseType($method);
        $route->setResponseType($returnType);

        // If we dont have a doc comment, theres nothing else to do.
        if ($method->getDocComment() === false) {
            return;
        }

        $parsed = $this->docFactory->create($method->getDocComment());

        $route->setDeprecated($parsed->hasTag('deprecated'));
        $route->setDescription($parsed->getDescription()->render() ?: null);
        $route->setSummary($parsed->getSummary());
    }

    /**
     * Tries to find the input type.
     *
     * @param \ReflectionMethod $method
     *
     * @return string|null
     */
    private function findRequestType(ReflectionMethod $method): ?string
    {
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getClass() === null) {
                continue;
            }

            $implements = \class_implements($parameter->getClass()->getName());
            $intersect = \array_intersect($this->requestInterfaces, $implements);

            if (\count($intersect) > 0) {
                return $parameter->getClass()->getName();
            }
        }

        return null;
    }

    /**
     * Finds the response type if it not an implementation of the excluded response
     * types.
     *
     * @param \ReflectionMethod $method
     *
     * @return string|null
     */
    private function findResponseType(ReflectionMethod $method): ?string
    {
        $returnType = $method->getReturnType();
        if ($returnType instanceof ReflectionNamedType === false ||
            $returnType->getName() === 'array' ||
            \class_exists($returnType->getName()) === false
        ) {
            return null;
        }

        /**
         * @var \ReflectionNamedType $returnType
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
         */
        $implements = \class_implements($returnType->getName());
        $intersect = \array_intersect($this->excludeResponses, $implements);

        if (\count($intersect) > 0) {
            return null;
        }

        return $returnType->getName();
    }
}
