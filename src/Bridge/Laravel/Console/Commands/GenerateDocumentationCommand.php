<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands;

use Illuminate\Console\Command;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

final class GenerateDocumentationCommand extends Command
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface
     */
    private $generator;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface $generator
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(GeneratorInterface $generator, SerializerInterface $serializer)
    {
        $this->description = 'Generates application documentation.';
        $this->signature = 'loyaltycorp:documentation:generate 
            {name : The applications name}
            {version : The version of the documentation}
            {examples? : The full path to the api-documentation-examples.json output file}';

        $this->generator = $generator;
        $this->serializer = $serializer;

        parent::__construct();
    }

    /**
     * Handles.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->getArgument('name');
        if (\is_string($name) === false) {
            // @codeCoverageIgnoreStart
            // Laravel's awesome command function typehints..
            throw new RuntimeException('The required option "name" was not provided or is not a string.');
            // @codeCoverageIgnoreEnd
        }

        $version = $this->getArgument('version');
        if (\is_string($version) === false) {
            // @codeCoverageIgnoreStart
            // Laravel's awesome command function typehints..
            throw new RuntimeException('The required option "version" was not provided or is not a string.');
            // @codeCoverageIgnoreEnd
        }

        $examples = $this->getExamples();

        $output = $this->generator->generate($name, $version, $examples);

        $this->output->write($output);
    }

    /**
     * Get option as a string or null.
     *
     * @param string $key The option to get
     *
     * @return string|null
     */
    private function getArgument(string $key): ?string
    {
        $option = $this->argument($key) ?: null;
        // If option is an array reset
        while (\is_array($option)) {
            // Code coverage is suppressed because it shouldn't be possible to pass an array
            // but Laravel typehints it is possible therefore needs to be handled
            $option = \reset($option); // @codeCoverageIgnore
        }

        return \is_scalar($option) === true ? (string)$option : null;
    }

    /**
     * Parses an examples.json file (path specified in command) and returns a RouteExamples
     * object.
     *
     * @return \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples|null
     */
    private function getExamples(): ?RouteExamples
    {
        $path = $this->getArgument('examples');

        if (\is_string($path) === false || \file_exists($path) === false) {
            return null;
        }

        $examples =  $this->serializer->deserialize(
            \file_get_contents($path),
            RouteExamples::class,
            'json'
        );

        // If the serialiser doesnt return the expected class we throw.
        if ($examples instanceof RouteExamples === false) {
            throw new RuntimeException('The serializer didnt return a RouteExamples object.');
        }

        /**
         * @var \LoyaltyCorp\ApiDocumenter\Routing\RouteExamples $examples
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */

        return $examples;
    }
}
