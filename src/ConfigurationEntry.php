<?php declare(strict_types=1);

namespace Quanta\Container;

final class ConfigurationEntry implements ConfigurationEntryInterface
{
    /**
     * The configuration to provide.
     *
     * @var \Quanta\Container\Configuration
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function configuration(): Configuration
    {
        return $this->configuration;
    }
}
