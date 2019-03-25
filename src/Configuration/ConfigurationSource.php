<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class ConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The configuration to provide.
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
    public function configuration(): ConfigurationInterface
    {
        return $this->configuration;
    }
}
