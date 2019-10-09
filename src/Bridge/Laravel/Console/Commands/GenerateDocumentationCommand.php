<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands;

use Illuminate\Console\Command;
use LoyaltyCorp\ApiDocumenter\Documentation\Generator;
use RuntimeException;

final class GenerateDocumentationCommand extends Command
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\Documentation\Generator
     */
    private $generator;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\Documentation\Generator $generator
     */
    public function __construct(Generator $generator)
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
            throw new RuntimeException('name must be a string');
        }

        $version = $this->argument('version');
        if (\is_string($version) === false) {
            throw new RuntimeException('version must be a string');
        }

        $output = $this->generator->generate($name, $version);

        $this->output->write($output);
    }
}
