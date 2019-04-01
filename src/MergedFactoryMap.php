<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedFactoryMap implements FactoryMapInterface
{
    /**
     * The factory maps.
     *
     * @var \Quanta\Container\FactoryMapInterface[]
     */
    private $maps;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface ...$maps
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
        return array_merge([], ...array_map(function ($map) {
            return $map->factories();
        }, $this->maps));
    }
}
