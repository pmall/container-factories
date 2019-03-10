<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Passes\MergedProcessingPass;

final class MergedConfiguration implements ConfigurationInterface
{
    /**
     * The array of configurations to merge.
     *
     * @var \Quanta\Container\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationInterface ...$configurations
     */
    public function __construct(ConfigurationInterface ...$configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @inheritdoc
     */
    public function map(): ConfiguredFactoryMap
    {
        $entries = Utils::plucked($this->configurations, 'map');

        return new ConfiguredFactoryMap(
            new MergedFactoryMap(...Utils::plucked($entries, 'map')),
            new MergedProcessingPass(...Utils::plucked($entries, 'pass'))
        );
    }
}
