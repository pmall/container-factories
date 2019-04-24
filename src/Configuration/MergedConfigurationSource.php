<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class MergedConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The array of configuration sources to merge.
     *
     * @var \Quanta\Container\Configuration\ConfigurationSourceInterface[]
     */
    private $sources;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationSourceInterface ...$sources
     */
    public function __construct(ConfigurationSourceInterface ...$sources)
    {
        $this->sources = $sources;
    }

    /**
     * @inheritdoc
     */
    public function configuration(): ConfigurationInterface
    {
        return new MergedConfiguration(...array_map(function ($source) {
            return $source->configuration();
        }, $this->sources));
    }
}
