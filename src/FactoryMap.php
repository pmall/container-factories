<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Configuration\ConfigurationInterface;

final class FactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $unit = $this->configuration->unit();

        $factories = $unit->factories();
        $pass = $unit->pass();

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
     * @param \Quanta\Container\ProcessingPassInterface $pass
     * @param string                                    ...$ids
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
     * @param \Quanta\Container\ProcessingPassInterface $pass
     * @param string                                    ...$ids
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
