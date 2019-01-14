<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Utils\ClassNameCollectionInterface;

final class ClassNameCollectionConfiguration implements ConfigurationInterface
{
    /**
     * The class name collection.
     *
     * @var \Quanta\Utils\ClassNameCollectionInterface
     */
    private $collection;

    /**
     * Constructor.
     *
     * @param \Quanta\Utils\ClassNameCollectionInterface $collection
     */
    public function __construct(ClassNameCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @inheritdoc
     */
    public function entries(): array
    {
        $classes = $this->collection->classes();
        $classes = array_filter($classes, [$this, 'filter']);

        $providers = array_map([$this, 'configuration'], $classes);

        return array_values($providers);
    }

    /**
     * Return an external service provider from the given service provider class
     * name.
     *
     * @param string $class
     * @return \Quanta\Container\ExternalServiceProvider
     */
    private function configuration(string $class): ExternalServiceProvider
    {
        return new ExternalServiceProvider(new $class);
    }

    /**
     * Return whether the given string is the name of a class implementing
     * ServiceProviderInterface.
     *
     * @param string $class
     * @return bool
     */
    private function filter(string $class): bool
    {
        return is_subclass_of($class, ServiceProviderInterface::class, true);
    }
}
