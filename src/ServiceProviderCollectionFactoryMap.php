<?php declare(strict_types=1);

namespace Quanta\Container;

final class ServiceProviderCollectionFactoryMap implements FactoryMapInterface
{
    /**
     * The service provider collections.
     *
     * @var \Quanta\Container\ServiceProviderCollectionInterface[]
     */
    private $collections;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ServiceProviderCollectionInterface ...$collections
     */
    public function __construct(ServiceProviderCollectionInterface ...$collections)
    {
        $this->collections = $collections;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $providers = array_map([$this, 'providers'], $this->collections);
        $providers = array_merge([], ...$providers);

        $map = new ServiceProviderFactoryMap(...$providers);

        return $map->factories();
    }

    /**
     * Return the service providers provided by the given collection.
     *
     * @param \Quanta\Container\ServiceProviderCollectionInterface $collection
     * @return \Interop\Container\ServiceProviderInterface[]
     */
    private function providers(ServiceProviderCollectionInterface $collection): array
    {
        return $collection->providers();
    }
}
