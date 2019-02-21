<?php declare(strict_types=1);

namespace Quanta\Container;

final class Configuration implements ConfigurationInterface
{
    /**
     * The factory map to provide.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The array of configuration passes to provide.
     *
     * @var \Quanta\Container\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface         $map
     * @param \Quanta\Container\ConfigurationPassInterface  ...$passes
     */
    public function __construct(FactoryMapInterface $map, ConfigurationPassInterface ...$passes)
    {
        $this->map = $map;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function map(): FactoryMapInterface
    {
        return $this->map;
    }

    /**
     * @inheritdoc
     */
    public function passes(): array
    {
        return $this->passes;
    }
}
