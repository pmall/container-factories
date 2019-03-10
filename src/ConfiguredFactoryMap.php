<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ProcessingPassInterface;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;

final class ConfiguredFactoryMap implements FactoryMapInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\Maps\FactoryMapInterface
     */
    private $map;

    /**
     * The processing pass.
     *
     * @var \Quanta\Container\Passes\ProcessingPassInterface
     */
    private $pass;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface        $map
     * @param \Quanta\Container\Passes\ProcessingPassInterface  $pass
     */
    public function __construct(FactoryMapInterface $map, ProcessingPassInterface $pass)
    {
        $this->map = $map;
        $this->pass = $pass;
    }

    /**
     * Return the factory map.
     *
     * @return \Quanta\Container\Maps\FactoryMapInterface
     */
    public function map(): FactoryMapInterface
    {
        return $this->map;
    }

    /**
     * Return the processing pass.
     *
     * @return \Quanta\Container\Passes\ProcessingPassInterface
     */
    public function pass(): ProcessingPassInterface
    {
        return $this->pass;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $factories = $this->map->factories();

        $ids = array_keys($factories);

        $factories+= $this->aliases(...$ids);
        $factories+= $this->tags(...$ids);

        foreach ($factories as $id => $factory) {
            $factories[$id] = $this->pass->processed($id, $factory);
        }

        return $factories;
    }

    /**
     * Return an array of aliases for the given ids.
     *
     * @param string ...$ids
     * @return array
     */
    private function aliases(string ...$ids): array
    {
        $factories = [];

        foreach ($ids as $id) {
            foreach ($this->pass->aliases($id) as $alias) {
                $factories[$alias] = new Alias($id);
            }
        }

        return $factories;
    }

    /**
     * Return an array of tags for the given ids.
     *
     * @param string ...$ids
     * @return array
     */
    private function tags(string ...$ids): array
    {
        $factories = [];

        foreach ($this->pass->tags(...$ids) as $tag => $ids) {
            $factories[$tag] = new Tag(...$ids);
        }

        return $factories;
    }
}
