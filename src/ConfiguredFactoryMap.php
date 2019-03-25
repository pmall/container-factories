<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Configuration\ConfigurationSourceInterface;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class ConfiguredFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration source.
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
        $configuration = $this->source->configuration()->entry();

        $map = $configuration->map();
        $pass = $configuration->pass();

        $factories = $map->factories();

        $ids = array_keys($factories);

        $factories+= $this->aliases($pass, ...$ids);
        $factories+= $this->tags($pass, ...$ids);

        foreach ($factories as $id => $factory) {
            $factories[$id] = $pass->processed($id, $factory);
        }

        return $factories;
    }

    /**
     * Return an array of aliases provided by the given configuration pass for
     * the given ids.
     *
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface    $pass
     * @param string                                                            ...$ids
     * @return array
     */
    private function aliases(ProcessingPassInterface $pass, string ...$ids): array
    {
        $factories = [];

        foreach ($ids as $id) {
            foreach ($pass->aliases($id) as $alias) {
                $factories[$alias] = new Alias($id);
            }
        }

        return $factories;
    }

    /**
     * Return an array of tags provided by the given configuration pass for the
     * given ids.
     *
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface    $pass
     * @param string                                                            ...$ids
     * @return array
     */
    private function tags(ProcessingPassInterface $pass, string ...$ids): array
    {
        $factories = [];

        foreach ($pass->tags(...$ids) as $tag => $ids) {
            $factories[$tag] = new Tag(...$ids);
        }

        return $factories;
    }
}
