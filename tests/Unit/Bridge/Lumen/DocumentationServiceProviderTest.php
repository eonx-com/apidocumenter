<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\ApiDocumenter\Unit\Bridge\Lumen;

use Doctrine\Common\Persistence\ManagerRegistry;
use Laravel\Lumen\Routing\Router;
use LoyaltyCorp\ApiDocumenter\Bridge\Lumen\DocumenterServiceProvider;
use LoyaltyCorp\ApiDocumenter\Bridge\Lumen\LumenRouteExtractor;
use LoyaltyCorp\ApiDocumenter\ClassUtils\ClassFinder;
use LoyaltyCorp\ApiDocumenter\ClassUtils\Interfaces\ClassFinderInterface;
use LoyaltyCorp\ApiDocumenter\ClassUtils\PropertyInfoExtractor;
use LoyaltyCorp\ApiDocumenter\Documentation\Generator;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\GeneratorInterface;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface;
use LoyaltyCorp\ApiDocumenter\Documentation\RoutesToSchemasConverter;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteEnhancerInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteExtractorInterface;
use LoyaltyCorp\ApiDocumenter\Routing\Interfaces\RouteToPathItemConverterInterface;
use LoyaltyCorp\ApiDocumenter\Routing\ReflectionRouteEnhancer;
use LoyaltyCorp\ApiDocumenter\Routing\RouteToPathItemConverter;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\CustomEntityRequestSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\EntityRequestSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\OpenApiTypeResolverInterface;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\PropertyTypeToSchemaConverterInterface;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\OpenApiTypeResolver;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\PropertyTypeToSchemaConverter;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Tests\EoneoPay\Utils\Stubs\Vendor\Laravel\ContainerStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Doctrine\ORM\ManagerRegistryStub;
use Tests\LoyaltyCorp\ApiDocumenter\Stubs\Vendor\Illuminate\Foundation\ApplicationStub;
use Tests\LoyaltyCorp\ApiDocumenter\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\ApiDocumenter\Bridge\Lumen\DocumenterServiceProvider
 */
final class DocumentationServiceProviderTest extends TestCase
{
    /**
     * Tests service provider.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testBindings(): void
    {
        $app = new ApplicationStub();
        $app->instance(Router::class, new Router($app));
        $app->instance(ManagerRegistry::class, new ManagerRegistryStub());
        $app->instance(
            NameConverterInterface::class,
            new CamelCaseToSnakeCaseNameConverter()
        );

        (new DocumenterServiceProvider($app))
            ->register();

        $bindings = [
            'apidocumenter_property_extractor' => PropertyInfoExtractor::class,
            ClassFinderInterface::class => ClassFinder::class,
            GeneratorInterface::class => Generator::class,
            OpenApiTypeResolverInterface::class => OpenApiTypeResolver::class,
            PropertyTypeToSchemaConverterInterface::class => PropertyTypeToSchemaConverter::class,
            RouteEnhancerInterface::class => ReflectionRouteEnhancer::class,
            RouteExtractorInterface::class => LumenRouteExtractor::class,
            RouteToPathItemConverterInterface::class => RouteToPathItemConverter::class,
            RoutesToSchemasConverterInterface::class => RoutesToSchemasConverter::class,
            EntityRequestSchemaBuilder::class => EntityRequestSchemaBuilder::class,
            ObjectSchemaBuilder::class => ObjectSchemaBuilder::class
        ];

        foreach ($bindings as $binding => $class) {
            self::assertInstanceOf($class, $app->make($binding));
        }
    }
}
