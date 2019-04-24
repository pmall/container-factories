<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Configuration\ConfigurationSourceInterface;

final class FactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\Configuration\ConfigurationSourceInterface
     */
    private $source;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationSourceInterface $source
     */
    public function __construct(ConfigurationSourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $configuration = $this->source->configuration();

        $factories = $configuration->factories();
        $mappers = $configuration->mappers();
        $extensions = $configuration->extensions();

        $ids = array_keys($factories);

        $factories+= array_map(function ($mapper) use ($ids) {
            return $this->mapped($mapper, ...$ids);
        }, $mappers);

        foreach ($factories as $id => $factory) {
            $factories[$id] = $this->extended($factory, ...($extensions[$id] ?? []));
        }

        return $factories;
    }

    /**
     * Return a tag from the given mapper and ids.
     *
     * @param callable  $mapper
     * @param string    ...$ids
     * @return callable
     */
    private function mapped(callable $mapper, string ...$ids): callable
    {
        return new Tag(...array_filter($ids, $mapper));
    }

    /**
     * Return an extension from the given callables.
     *
     * @param callable $factory
     * @param callable ...$extensions
     * @return callable
     */
    private function extended(callable $factory, callable ...$extensions): callable
    {
        return array_reduce($extensions, function ($factory, $extension) {
            return new Extension($factory, $extension);
        }, $factory);
    }
}
