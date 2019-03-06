<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Utils;
use Quanta\Container\MergedFactoryMap;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\FactoryMapInterface;

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
        $maps = Utils::plucked($this->configurations, 'map');

        return new ProcessedFactoryMap(
            new MergedFactoryMap(
                ...Utils::plucked($maps, 'map')
            ),
            ...array_merge([], ...Utils::plucked($maps, 'passes'))
        );
    }
}
