<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands;

use Illuminate\Console\Command;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use RuntimeException;

final class GenerateDocumentationCommand extends Command
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface
     */
    private $generator;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface $generator
     */
    public function __construct(GeneratorInterface $generator)
    {
        parent::__construct();

        $this->description = 'Generates application documentation.';
        $this->signature = 'loyaltycorp:documentation:generate 
            {name : The applications name}
            {version : The version of the documentation}';

        $this->generator = $generator;
    }

    /**
     * Handles.
     *
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
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

        $output = $this->generator->generate($name, $version);

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
}
