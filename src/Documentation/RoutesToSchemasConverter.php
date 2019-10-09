<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Documentation;

use LoyaltyCorp\ApiDocumenter\ClassUtils\Interfaces\ClassFinderInterface;
use LoyaltyCorp\ApiDocumenter\Documentation\Exceptions\NoSchemaBuilderFoundException;
use LoyaltyCorp\ApiDocumenter\Documentation\Interfaces\RoutesToSchemasConverterInterface;
use function LoyaltyCorp\ApiDocumenter\SchemaBuilders\buildReference;

final class RoutesToSchemasConverter implements RoutesToSchemasConverterInterface
{
    /**
     * @var \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface[]
     */
    private $builders;

    /**
     * @var \LoyaltyCorp\ApiDocumenter\ClassUtils\Interfaces\ClassFinderInterface
     */
    private $classFinder;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\SchemaBuilderInterface[] $builders
     * @param \LoyaltyCorp\ApiDocumenter\ClassUtils\Interfaces\ClassFinderInterface $classFinder
     */
    public function __construct(
        array $builders,
        ClassFinderInterface $classFinder
    ) {
        $this->builders = $builders;
        $this->classFinder = $classFinder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\ApiDocumenter\Documentation\Exceptions\NoSchemaBuilderFoundException
     */
    public function convert(array $routes): array
    {
        $initialClasses = [];

        foreach ($routes as $route) {
            $initialClasses[] = $route->getRequestType();
            $initialClasses[] = $route->getResponseType();
        }

        $initialClasses = \array_filter(\array_unique($initialClasses));
        $classes = $this->classFinder->extract($initialClasses);

        $schemas = [];

        foreach ($classes as $class) {
            foreach ($this->builders as $builder) {
                if ($builder->supports($class) === false) {
                    continue;
                }

                $ref = buildReference($class, false);
                $schemas[$ref] = $builder->buildSchema($class);

                continue 2;
            }

            throw new NoSchemaBuilderFoundException(
                \sprintf('No schema builder was found for "%s"', $class)
            );
        }

        return $schemas;
    }
}
