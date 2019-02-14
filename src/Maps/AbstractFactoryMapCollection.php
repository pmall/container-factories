<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

abstract class AbstractFactoryMapCollection implements FactoryMapInterface
{
    /**
     * The factory maps.
     *
     * @var \Quanta\Container\Maps\FactoryMapInterface[]
     */
    private $maps;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface ...$maps
     */
    public function __construct(FactoryMapInterface ...$maps)
    {
        $this->maps = $maps;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $xs = array_map([$this, 'nestedFactories'], $this->maps);
        $xs = array_merge_recursive([], ...$xs);

        return array_map([$this, 'factory'], $xs);
    }

    /**
     * Return an array containing the given factory.
     *
     * @param callable $factory
     * @return array
     */
    private function nested(callable $factory): array
    {
        return [$factory];
    }

    /**
     * Return an array of nested factories from the array of factories provided
     * by the given factory map.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface $map
     * @return array
     */
    private function nestedFactories(FactoryMapInterface $map): array
    {
        return array_map([$this, 'nested'], $map->factories());
    }

    /**
     * Reduce the given array of factories as a single one.
     *
     * @param callable[] $factories
     * @return callable
     */
    abstract protected function factory(array $factories): callable;
}
