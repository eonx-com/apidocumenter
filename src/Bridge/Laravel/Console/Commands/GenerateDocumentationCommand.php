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
        $name = $this->argument('name');
        if (\is_string($name) === false) {
            // @codeCoverageIgnoreStart
            // Laravel's awesome command function typehints..
            throw new RuntimeException('name must be a string');
            // @codeCoverageIgnoreEnd
        }

        $version = $this->argument('version');
        if (\is_string($version) === false) {
            // @codeCoverageIgnoreStart
            // Laravel's awesome command function typehints..
            throw new RuntimeException('version must be a string');
            // @codeCoverageIgnoreEnd
        }

        $output = $this->generator->generate($name, $version);

        $this->output->write($output);
    }
}
