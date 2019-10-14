<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Routing;

final class RouteExamples
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\Routing\RouteExample[][][]
     */
    private $examples;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Routing\RouteExample[] $examples
     */
    public function __construct(array $examples)
    {
        $sortedExamples = [];

        foreach ($examples as $example) {
            $method = $example->getMethod();
            $path = $example->getPath();

            if (\array_key_exists($method, $sortedExamples) === false) {
                $sortedExamples[$method] = [];
            }

            if (\array_key_exists($path, $sortedExamples[$method]) === false) {
                $sortedExamples[$method][$path] = [];
            }

            $sortedExamples[$method][$path][] = $example;
        }

        $this->examples = $sortedExamples;
    }

    /**
     * Returns any examples for a method/path.
     *
     * @param string $method
     * @param string $path
     *
     * @return \LoyaltyCorp\ApiDocumenter\Routing\RouteExample[]
     */
    public function getExamples(string $method, string $path): array
    {
        return $this->examples[$method][$path] ?? [];
    }
}
