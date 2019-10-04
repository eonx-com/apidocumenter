<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

final class Route
{
    /**
     * The controller class.
     *
     * @var string
     */
    private $controllerClass;

    /**
     * The controller method.
     *
     * @var string
     */
    private $controllerMethod;

    /**
     * If the route is deprecated.
     *
     * @var bool
     */
    private $deprecated = false;

    /**
     * The routes description.
     *
     * @var string|null
     */
    private $description;

    /**
     * The route method.
     *
     * @var string
     */
    private $method;

    /**
     * An array of route parameters that apply to this route.
     *
     * @var string[]
     */
    private $parameters;

    /**
     * The routes path.
     *
     * @var string
     */
    private $path;

    /**
     * The request class for the route controller.
     *
     * @var string|null
     */
    private $requestType;

    /**
     * The return type class of the route controller.
     *
     * @var string|null
     */
    private $responseType;

    /**
     * The route summary.
     *
     * @var string|null
     */
    private $summary;

    /**
     * Constructor.
     *
     * @param string $controllerClass
     * @param string $controllerMethod
     * @param string $method
     * @param string $path
     * @param string[]|null $parameters
     */
    public function __construct(
        string $controllerClass,
        string $controllerMethod,
        string $method,
        string $path,
        ?array $parameters = null
    ) {
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
        $this->method = $method;
        $this->path = $path;
        $this->parameters = $parameters ?? [];
    }

    /**
     * Returns the controller class.
     *
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    /**
     * Returns the controller method.
     *
     * @return string
     */
    public function getControllerMethod(): string
    {
        return $this->controllerMethod;
    }

    /**
     * Returns the route description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the route method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the route path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns any parameters in the route.
     *
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the request class that is used for this controller if one exists.
     *
     * @return string|null
     */
    public function getRequestType(): ?string
    {
        return $this->requestType;
    }

    /**
     * Returns a class that represents the response type of this route.
     *
     * @return string|null
     */
    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    /**
     * Returns the route summary.
     *
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * If the route is deprecated.
     *
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }

    /**
     * If the route is deprecated.
     *
     * @param bool $deprecated
     *
     * @return void
     */
    public function setDeprecated(bool $deprecated): void
    {
        $this->deprecated = $deprecated;
    }

    /**
     * Sets the description.
     *
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Sets the request type.
     *
     * @param string|null $requestType
     *
     * @return void
     */
    public function setRequestType(?string $requestType): void
    {
        $this->requestType = $requestType;
    }

    /**
     * Sets the response type.
     *
     * @param string|null $responseType
     *
     * @return void
     */
    public function setResponseType(?string $responseType): void
    {
        $this->responseType = $responseType;
    }

    /**
     * Sets the summary of the route.
     *
     * @param string|null $summary
     *
     * @return void
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }
}
