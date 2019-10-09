<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Bridge\Lumen;

use Illuminate\Contracts\Container\Container;
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
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\ObjectSchemaBuilder;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\OpenApiTypeResolver;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\PropertyTypeToSchemaConverter;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor as BasePropertyInfoExtractor;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Required to configure
 */
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
                $phpDocExtractor = new PhpDocExtractor();
                $reflectionExtractor = new ReflectionExtractor(
                    null,
                    null,
                    null,
                    true,
                    ReflectionExtractor::ALLOW_PRIVATE |
                    ReflectionExtractor::ALLOW_PROTECTED |
                    ReflectionExtractor::ALLOW_PUBLIC
                );
                $propertyInfo = new BasePropertyInfoExtractor(
                    [$reflectionExtractor],
                    [$phpDocExtractor, $reflectionExtractor],
                    [$phpDocExtractor],
                    [],
                    []
                );

                return new PropertyInfoExtractor($propertyInfo);
            }
        );
        $this->app->bind(ClassFinderInterface::class, static function (Container $app): ClassFinder {
            return new ClassFinder(
                $app->make('apidocumenter_property_extractor'),
                []
            );
        });
        $this->app->instance(DocBlockFactory::class, DocBlockFactory::createInstance());
        $this->app->bind(GeneratorInterface::class, Generator::class);
        $this->app->bind(OpenApiTypeResolverInterface::class, OpenApiTypeResolver::class);
        $this->app->bind(PropertyTypeToSchemaConverterInterface::class, PropertyTypeToSchemaConverter::class);
        $this->app->bind(RouteEnhancerInterface::class, static function (Container $app): ReflectionRouteEnhancer {
            return new ReflectionRouteEnhancer($app->make(DocBlockFactory::class), [], []);
        });
        $this->app->bind(RouteExtractorInterface::class, LumenRouteExtractor::class);
        $this->app->bind(RouteToPathItemConverterInterface::class, RouteToPathItemConverter::class);
        $this->app->bind(
            RoutesToSchemasConverterInterface::class,
            static function (Container $app): RoutesToSchemasConverter {
                $builderIterator = $app->tagged('apidocumenter_schema_builder');
                $builders = [];
                foreach ($builderIterator as $builder) {
                    if ($builder instanceof SchemaBuilderInterface === false) {
                        continue;
                    }

                    $builders[] = $builder;
                }

                return new RoutesToSchemasConverter(
                    $builders,
                    $app->make(ClassFinderInterface::class)
                );
            }
        );

        $this->app->bind('apidocumenter_name_converter', CamelCaseToSnakeCaseNameConverter::class);

        $this->app->bind(EntityRequestSchemaBuilder::class);
        $this->app->bind(ObjectSchemaBuilder::class, static function (Container $app): ObjectSchemaBuilder {
            return new ObjectSchemaBuilder(
                $app->make('apidocumenter_name_converter'),
                $app->make('apidocumenter_property_extractor'),
                $app->make(PropertyTypeToSchemaConverterInterface::class)
            );
        });

        $this->app->tag([
            EntityRequestSchemaBuilder::class,
            ObjectSchemaBuilder::class
        ], ['apidocumenter_schema_builder']);
    }
}
