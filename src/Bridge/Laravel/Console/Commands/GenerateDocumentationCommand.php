<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands;

use Illuminate\Console\Command;
use LoyaltyCorp\ApiDocumenter\Documentation\Generator;

final class GenerateDocumentationCommand extends Command
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * Constructor.
     *
     * @param Generator $generator
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
     * @return void
     *
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function handle(): void
    {
        $output = $this->generator->generate(
            $this->argument('name'),
            $this->argument('version')
        );

        $this->output->write($output);
    }
}
