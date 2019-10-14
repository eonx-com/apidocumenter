<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

final class RouteExample
{
    /**
     * A longer description of the example.
     *
     * @var string|null
     */
    private $description;

    /**
     * The method of the request.
     *
     * @var string
     */
    private $method;

    /**
     * The Path the request was made to.
     *
     * @var string
     */
    private $path;

    /**
     * Stores the JSON representation of the example.
     *
     * @var string|null
     */
    private $requestData;

    /**
     * JSON string response.
     *
     * @var string|null
     */
    private $responseData;

    /**
     * The status code of the response.
     *
     * @var int
     */
    private $responseStatusCode;

    /**
     * The one line summary of the example.
     *
     * @var string
     */
    private $summary;

    /**
     * Constructor.
     *
     * @param string|null $description
     * @param string $method
     * @param string $path
     * @param string|null $requestData
     * @param string|null $responseData
     * @param int $responseStatusCode
     * @param string $summary
     */
    public function __construct(
        ?string $description,
        string $method,
        string $path,
        ?string $requestData,
        ?string $responseData,
        int $responseStatusCode,
        string $summary
    ) {
        $this->description = $description;
        $this->method = $method;
        $this->path = $path;
        $this->requestData = $requestData;
        $this->responseData = $responseData;
        $this->responseStatusCode = $responseStatusCode;
        $this->summary = $summary;
    }

    /**
     * Returns description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns request data.
     *
     * @return string|null
     */
    public function getRequestData(): ?string
    {
        return $this->requestData;
    }

    /**
     * Returns response data.
     *
     * @return string|null
     */
    public function getResponseData(): ?string
    {
        return $this->responseData;
    }

    /**
     * Returns status code.
     *
     * @return int
     */
    public function getResponseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    /**
     * Returns summary.
     *
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }
}
