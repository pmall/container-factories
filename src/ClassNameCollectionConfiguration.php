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
     * The pattern class names must match.
     *
     * @var string
     */
    private $pattern;

    /**
     * The blacklisted patterns class names must not match.
     *
     * @var string[]
     */
    private $blacklisted;

    /**
     * Constructor.
     *
     * This package service provider and service provider interface are always
     * blacklisted.
     *
     * @param \Quanta\Utils\ClassNameCollectionInterface    $collection
     * @param string                                        $pattern
     * @param string                                        ...$blacklisted
     */
    public function __construct(ClassNameCollectionInterface $collection, string $pattern = '/.*?/', string ...$blacklisted)
    {
        $this->collection = $collection;
        $this->pattern = $pattern;
        $this->blacklisted = $blacklisted;
    }

    /**
     * @inheritdoc
     */
    public function providers(): array
    {
        $classes = $this->collection->classes();
        $classes = array_filter($classes, [$this, 'filter']);
        $classes = array_values($classes);

        return array_map([$this, 'provider'], $classes);
    }

    /**
     * Return an instance of the given service provider class name.
     *
     * @param string $class
     * @return \Interop\Container\ServiceProviderInterface
     */
    private function provider(string $class): ServiceProviderInterface
    {
        return new $class;
    }

    /**
     * Return whether the given string is the name of a class implementing
     * ServiceProviderInterface and is matching the pattern but not any
     * blacklist pattern.
     *
     * @param string $class
     * @return bool
     */
    private function filter(string $class): bool
    {
        if (! is_subclass_of($class, ServiceProviderInterface::class, true)) {
            return false;
        }

        if (preg_match($this->pattern, $class) === 0) {
            return false;
        }

        foreach ($this->blacklisted as $blacklisted) {
            if (fnmatch($blacklisted, $class, FNM_NOESCAPE)) {
                return false;
            }
        }

        return true;
    }
}
