<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\Generator\Interfaces;

/**
 * The class finder is used to traverse a graph of object classes and return
 * a list of all classes that were found during that traversal.
 *
 * It is used to have a list of all objects that may be returned by the API
 * so that Schema objects can be built for any possible returned values.
 *
 * The class can be configured to skip objects that should be considered as
 * value objects (and that the serialiser will turn into scalar objects).
 */
interface ClassFinderInterface
{
    /**
     * Returns all classes that are related to the input classes by traversing all
     * properties on the initial classes looking for additional classes.
     *
     * @param string[] $classes
     *
     * @return string[]
     */
    public function extract(array $classes): array;
}
