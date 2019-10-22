<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Bridge\Laravel\Console\Commands;

use Illuminate\Console\OutputStyle;
use LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands\GenerateDocumentationCommand;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExample;
use LoyaltyCorp\ApiDocumenter\Routing\RouteExamples;
use ReflectionClass;
use RuntimeException;
use stdClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Documentation\GeneratorStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Symfony\Serializer\SerializerStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Bridge\Laravel\Console\Commands\GenerateDocumentationCommand
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) required to test
 */
final class GenerateDocumentationCommandTest extends TestCase
{
    /**
     * Tests the command runs when there are no examples.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCommandNoExamples(): void
    {
        $generator = new GeneratorStub();
        $input = new ArrayInput([
            'name' => 'Application Name',
            'version' => '1.2.3',
        ], new InputDefinition([
            new InputArgument('name'),
            new InputArgument('version'),
            new InputArgument('examples'),
        ]));
        $output = new BufferedOutput();

        $serializer = new SerializerStub(null);

        $command = new GenerateDocumentationCommand($generator, $serializer);

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

    /**
     * Tests the command runs when when an examples json file is provided.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCommandExamples(): void
    {
        $generator = new GeneratorStub();
        $input = new ArrayInput([
            'name' => 'Application Name',
            'version' => '1.2.3',
            'examples' => __DIR__ . '/Fixtures/examples.json',
        ], new InputDefinition([
            new InputArgument('name'),
            new InputArgument('version'),
            new InputArgument('examples'),
        ]));
        $output = new BufferedOutput();

        $routeExamples = [new RouteExample('', '', '', null, null, 1, '')];
        $serializer = new SerializerStub($routeExamples);
        $expectedJson = [
            'data' => '{"example": "json"}
',
            'type' => RouteExample::class.'[]',
            'format' => 'json',
            'context' => null,
        ];
        $expectedGenerate = [
            'name' => 'Application Name',
            'version' => '1.2.3',
            'examples' => new RouteExamples($routeExamples),
        ];

        $command = new GenerateDocumentationCommand($generator, $serializer);

        $reflClass = new ReflectionClass(GenerateDocumentationCommand::class);
        $inputProp = $reflClass->getProperty('input');
        $inputProp->setAccessible(true);
        $inputProp->setValue($command, $input);
        $outputProp = $reflClass->getProperty('output');
        $outputProp->setAccessible(true);
        $outputProp->setValue($command, new OutputStyle($input, $output));

        $command->handle();

        self::assertEquals([$expectedGenerate], $generator->getCalls());
        self::assertSame([$expectedJson], $serializer->getCalls());
        self::assertSame('generated output', $output->fetch());
    }

    /**
     * Tests the command fails when the serialiser doesnt return what we expect.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCommandBadSerializer(): void
    {
        $generator = new GeneratorStub();
        $input = new ArrayInput([
            'name' => 'Application Name',
            'version' => '1.2.3',
            'examples' => __DIR__ . '/Fixtures/examples.json',
        ], new InputDefinition([
            new InputArgument('name'),
            new InputArgument('version'),
            new InputArgument('examples'),
        ]));
        $output = new BufferedOutput();

        $serializer = new SerializerStub(new stdClass());

        $command = new GenerateDocumentationCommand($generator, $serializer);

        $reflClass = new ReflectionClass(GenerateDocumentationCommand::class);
        $inputProp = $reflClass->getProperty('input');
        $inputProp->setAccessible(true);
        $inputProp->setValue($command, $input);
        $outputProp = $reflClass->getProperty('output');
        $outputProp->setAccessible(true);
        $outputProp->setValue($command, new OutputStyle($input, $output));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The serializer didnt return an array.');

        $command->handle();
    }
}
