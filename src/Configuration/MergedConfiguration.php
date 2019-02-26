<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\FactoryMapInterface;

use Quanta\Container\Helpers\Pluck;

final class MergedConfiguration implements ConfigurationInterface
{
    /**
     * The array of configurations to merge.
     *
     * @var \Quanta\Container\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface ...$configurations
     */
    public function __construct(ConfigurationInterface ...$configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @inheritdoc
     */
    public function map(): ProcessedFactoryMap
    {
        $maps = array_map(new Pluck('map'), $this->configurations);

        return new ProcessedFactoryMap(
            new MergedFactoryMap(...array_map(new Pluck('map'), $maps)),
            ...array_merge([], ...array_map(new Pluck('passes'), $maps))
        );
    }
}
