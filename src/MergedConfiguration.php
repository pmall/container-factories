<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Passes\MergedExtensionPass;
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
    public function entry(): ConfigurationEntry
    {
        $entries = Utils::plucked($this->configurations, 'entry');

        return new ConfigurationEntry(
            new MergedFactoryMap(...Utils::plucked($entries, 'map')),
            new MergedProcessingPass(...Utils::plucked($entries, 'processing')),
            new MergedExtensionPass(...Utils::plucked($entries, 'extension'))
        );
    }
}
