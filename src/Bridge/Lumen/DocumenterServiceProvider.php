<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Lumen;

use Illuminate\Support\ServiceProvider;
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

final class DocumenterServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(
            'apidocumenter_property_extractor',
            static function (): PropertyInfoExtractor {
                return new PropertyInfoExtractor();
            }
        );
        $this->app->bind(ClassFinderInterface::class, ClassFinder::class);
        $this->app->bind(GeneratorInterface::class, Generator::class);
        $this->app->bind(OpenApiTypeResolverInterface::class, OpenApiTypeResolver::class);
        $this->app->bind(PropertyTypeToSchemaConverterInterface::class, PropertyTypeToSchemaConverter::class);
        $this->app->bind(RouteEnhancerInterface::class, ReflectionRouteEnhancer::class);
        $this->app->bind(RouteExtractorInterface::class, LumenRouteExtractor::class);
        $this->app->bind(RouteToPathItemConverterInterface::class, RouteToPathItemConverter::class);
        $this->app->bind(
            RoutesToSchemasConverterInterface::class,
            RoutesToSchemasConverter::class
        );

        $builders = [
            CustomEntityRequestSchemaBuilder::class,
            EntityRequestSchemaBuilder::class,
            ObjectSchemaBuilder::class,
        ];

        foreach ($builders as $builder) {
            $this->app->bind($builder);
        }

        $this->app->tag($builders, ['apidocumenter_schema_builder']);
    }
}
