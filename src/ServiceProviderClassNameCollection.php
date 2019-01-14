<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Utils\Psr4Namespace;
use Quanta\Utils\VendorDirectory;
use Quanta\Utils\ClassNameCollection;
use Quanta\Utils\ClassNameCollectionInterface;
use Quanta\Utils\WhitelistedClassNameCollection;
use Quanta\Utils\BlacklistedClassNameCollection;

final class ServiceProviderClassNameCollection implements ConfigurationInterface
{
    /**
     * The class name collection.
     *
     * @var \Quanta\Utils\ClassNameCollectionInterface
     */
    private $collection;

    /**
     * Return a new ServiceProviderClassNameCollection from the given class
     * names.
     *
     * @param string ...$classes
     * @return \Quanta\Container\ServiceProviderClassNameCollection
     */
    public static function fromClassNames(string ...$classes): ServiceProviderClassNameCollection
    {
        return new ServiceProviderClassNameCollection(
            new ClassNameCollection(...$classes)
        );
    }

    /**
     * Return a new ServiceProviderClassNameCollection from the Psr-4 namespace
     * defined by the given root namespace and directory.
     *
     * @param string $namespace
     * @param string $directory
     * @return \Quanta\Container\ServiceProviderClassNameCollection
     */
    public static function fromPsr4Namespace(string $namespace, string $directory): ServiceProviderClassNameCollection
    {
        return new ServiceProviderClassNameCollection(
            new Psr4Namespace($namespace, $directory)
        );
    }

    /**
     * Return a new ServiceProviderClassNameCollection from the vendor directory
     * located at the given path.
     *
     * @param string $path
     * @return \Quanta\Container\ServiceProviderClassNameCollection
     */
    public static function fromVendorDirectory(string $path): ServiceProviderClassNameCollection
    {
        return new ServiceProviderClassNameCollection(
            new VendorDirectory($path)
        );
    }

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
     * Return a new service provider class name collection filtering class names
     * with the given whitelist patterns.
     *
     * @param string ...$patterns
     * @return \Quanta\Container\ServiceProviderClassNameCollection
     */
    public function withWhitelist(string ...$patterns): ServiceProviderClassNameCollection
    {
        return new ServiceProviderClassNameCollection(
            new WhitelistedClassNameCollection($this->collection, ...$patterns)
        );
    }

    /**
     * Return a new service provider class name collection filtering class names
     * with the given blacklist patterns.
     *
     * @param string ...$patterns
     * @return \Quanta\Container\ServiceProviderClassNameCollection
     */
    public function withBlacklist(string ...$patterns): ServiceProviderClassNameCollection
    {
        return new ServiceProviderClassNameCollection(
            new BlacklistedClassNameCollection($this->collection, ...$patterns)
        );
    }

    /**
     * @inheritdoc
     */
    public function entries(): array
    {
        $classes = $this->collection->classes();
        $classes = array_filter($classes, [$this, 'filter']);

        $providers = array_map([$this, 'entry'], $classes);

        return array_values($providers);
    }

    /**
     * Return an service provider configuration entry from the given service
     * provider class name.
     *
     * @param string $class
     * @return \Quanta\Container\ServiceProviderConfigurationEntry
     */
    private function entry(string $class): ServiceProviderConfigurationEntry
    {
        return new ServiceProviderConfigurationEntry(new $class);
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
        return class_exists($class)
            && is_subclass_of($class, ServiceProviderInterface::class, true);
    }
}
