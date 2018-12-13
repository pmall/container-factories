<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Utils\ClassNameCollectionInterface;

final class ServiceProviderCollection implements ServiceProviderCollectionInterface
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
     * @param string
     */
    private $pattern;

    /**
     * The blacklisted classes.
     *
     * @param string[]
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
        $this->blacklisted = array_merge($blacklisted, [
            ServiceProvider::class,
            ServiceProviderInterface::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function providers(): array
    {
        return iterator_to_array($this->instances());
    }

    /**
     * Return whether the given class name is matching the pattern and not
     * matched by any blacklist pattern.
     *
     * @param string $class
     * @return bool
     */
    private function isMatching(string $class): bool
    {
        if (preg_match($this->pattern, $class) === 1) {
            foreach ($this->blacklisted as $blacklisted) {
                if (fnmatch($blacklisted, $class, FNM_NOESCAPE)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Yield the class names matching the pattern and not matching any blacklist
     * pattern.
     *
     * @return \Generator
     */
    private function matching(): \Generator
    {
        foreach ($this->collection->classes() as $class) {
            if ($this->isMatching($class)) {
                yield $class;
            }
        }
    }

    /**
     * Yield the names of the classes implementing ServiceProviderInterface.
     *
     * @return \Generator
     */
    private function implementations(): \Generator
    {
        foreach ($this->matching() as $class) {
            if (is_a($class, ServiceProviderInterface::class, true)) {
                yield $class;
            }
        }
    }

    /**
     * Yield the service provider instances.
     *
     * @return \Generator
     */
    private function instances()
    {
        foreach ($this->implementations() as $class) {
            yield new $class;
        }
    }
}
