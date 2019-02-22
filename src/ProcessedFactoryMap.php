<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Configuration\ConfigurationPassInterface;

final class ProcessedFactoryMap implements FactoryMapInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The configuration passes used to process the factories.
     *
     * @var \Quanta\Container\Configuration\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface                         $map
     * @param \Quanta\Container\Configuration\ConfigurationPassInterface    ...$passes
     */
    public function __construct(FactoryMapInterface $map, ConfigurationPassInterface ...$passes)
    {
        $this->map = $map;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_reduce($this->passes, [$this, 'reduced'], $this->map->factories());
    }

    /**
     * Return the associative array of factories provided by the given
     * configuration pass.
     *
     * @param callable[]                                                    $factories
     * @param \Quanta\Container\Configuration\ConfigurationPassInterface    $pass
     * @return callable[]
     */
    private function reduced(array $factories, ConfigurationPassInterface $pass): array
    {
        return $pass->processed($factories);
    }
}
