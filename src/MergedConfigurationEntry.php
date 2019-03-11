<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Passes\MergedProcessingPass;

final class MergedConfigurationEntry implements ConfigurationEntryInterface
{
    /**
     * The array of configuration entries to merge.
     *
     * @var \Quanta\Container\ConfigurationEntryInterface[]
     */
    private $entries;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface ...$entries
     */
    public function __construct(ConfigurationEntryInterface ...$entries)
    {
        $this->entries = $entries;
    }

    /**
     * @inheritdoc
     */
    public function configuration(): Configuration
    {
        $configurations = Utils::plucked($this->entries, 'configuration');

        return new Configuration(
            new MergedFactoryMap(...Utils::plucked($configurations, 'map')),
            new MergedProcessingPass(...Utils::plucked($configurations, 'pass'))
        );
    }
}
