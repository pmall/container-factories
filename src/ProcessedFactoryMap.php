<?php declare(strict_types=1);

namespace Quanta\Container;

final class ProcessedFactoryMap implements FactoryMapInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The processing passes used to process the factories.
     *
     * @var \Quanta\Container\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface     $map
     * @param \Quanta\Container\ProcessingPassInterface ...$passes
     */
    public function __construct(FactoryMapInterface $map, ProcessingPassInterface ...$passes)
    {
        $this->map = $map;
        $this->passes = $passes;
    }

    /**
     * Return the factory map.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function map(): FactoryMapInterface
    {
        return $this->map;
    }

    /**
     * Return the processing passes.
     *
     * @return \Quanta\Container\ProcessingPassInterface[]
     */
    public function passes(): array
    {
        return $this->passes;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_reduce($this->passes, [$this, 'reduced'], $this->map->factories());
    }

    /**
     * Return the associative array of factories processed by the given
     * configuration pass.
     *
     * @param callable[]                                $factories
     * @param \Quanta\Container\ProcessingPassInterface $pass
     * @return callable[]
     */
    private function reduced(array $factories, ProcessingPassInterface $pass): array
    {
        return $pass->processed($factories);
    }
}
