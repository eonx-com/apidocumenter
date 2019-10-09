<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Bridge\Laravel\Console\Commands;

use Illuminate\Console\OutputStyle;
use LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands\GenerateDocumentationCommand;
use ReflectionClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation\GeneratorStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands\GenerateDocumentationCommand
 */
final class GenerateDocumentationCommandTest extends TestCase
{
    /**
     * Tests command.
     *
     * @return void
     *
     * @throws \ReflectionException
     * @throws \cebe\openapi\exceptions\TypeErrorException
     */
    public function testCommand(): void
    {
        $generator = new GeneratorStub();
        $input = new ArrayInput([
            'name' => 'Application Name',
            'version' => '1.2.3',
        ], new InputDefinition([
            new InputArgument('name'),
            new InputArgument('version'),
        ]));
        $output = new BufferedOutput();

        $command = new GenerateDocumentationCommand($generator);

        $reflClass = new ReflectionClass(GenerateDocumentationCommand::class);
        $inputProp = $reflClass->getProperty('input');
        $inputProp->setAccessible(true);
        $inputProp->setValue($command, $input);
        $outputProp = $reflClass->getProperty('output');
        $outputProp->setAccessible(true);
        $outputProp->setValue($command, new OutputStyle($input, $output));

        $command->handle();

        self::assertSame('generated output', $output->fetch());
    }
}
