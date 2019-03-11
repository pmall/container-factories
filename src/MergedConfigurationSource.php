<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The array of configuration sources to merge.
     *
     * @var \Quanta\Container\ConfigurationSourceInterface[]
     */
    private $sources;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationSourceInterface ...$sources
     */
    public function __construct(ConfigurationSourceInterface ...$sources)
    {
        $this->sources = $sources;
    }

    /**
     * @inheritdoc
     */
    public function entry(): ConfigurationEntryInterface
    {
        return new MergedConfigurationEntry(
            ...Utils::plucked($this->sources, 'entry')
        );
    }
}
